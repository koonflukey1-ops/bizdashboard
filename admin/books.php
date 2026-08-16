<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';

require_admin();

$pdo = db();
$edit = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = post('action');

    if ($action === 'save') {
        $id = (int) post('id');

        $cover = post('existing_cover') ?: null;

        if (!empty($_FILES['cover']['name'])) {
            $f = $_FILES['cover'];

            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            $mime = (new finfo(FILEINFO_MIME_TYPE))
                ->file($f['tmp_name']);

            if (
                !isset($allowed[$mime]) ||
                $f['size'] > 3 * 1024 * 1024
            ) {
                flash(
                    'ไฟล์ปกต้องเป็น JPG, PNG หรือ WebP และไม่เกิน 3MB',
                    'error'
                );

                redirect('admin/books.php');
            }

            $name =
                bin2hex(random_bytes(10))
                . '.'
                . $allowed[$mime];

            move_uploaded_file(
                $f['tmp_name'],
                __DIR__ . '/../uploads/books/' . $name
            );

            $cover = 'uploads/books/' . $name;
        }

        $data = [
            post('title'),
            post('author'),
            (int) post('category_id'),
            post('isbn') ?: null,
            post('publisher'),
            post('publication_year') ?: null,
            (float) post('price'),
            post('description'),
            $cover
        ];

        if ($id) {
            $data[] = $id;

            $pdo->prepare(
                'UPDATE books
                SET
                    title=?,
                    author=?,
                    category_id=?,
                    isbn=?,
                    publisher=?,
                    publication_year=?,
                    rental_price_per_day=?,
                    description=?,
                    cover_image=?
                WHERE id=?'
            )->execute($data);

            log_activity('update', 'book', $id);

            flash('แก้ไขหนังสือสำเร็จ');
        } else {
            $pdo->prepare(
                'INSERT INTO books(
                    title,
                    author,
                    category_id,
                    isbn,
                    publisher,
                    publication_year,
                    rental_price_per_day,
                    description,
                    cover_image
                )
                VALUES(?,?,?,?,?,?,?,?,?)'
            )->execute($data);

            $id = (int) $pdo->lastInsertId();

            log_activity('create', 'book', $id);

            flash('เพิ่มหนังสือสำเร็จ');
        }

        redirect('admin/books.php');
    }

    if ($action === 'delete') {
        $id = (int) post('id');

        $stmt = $pdo->prepare(
            "SELECT
                status,
                (
                    SELECT COUNT(*)
                    FROM reservations
                    WHERE book_id=?
                    AND status IN('pending','approved')
                ) active_res,
                (
                    SELECT COUNT(*)
                    FROM reservations
                    WHERE book_id=?
                ) all_res,
                (
                    SELECT COUNT(*)
                    FROM rentals
                    WHERE book_id=?
                ) all_rentals
            FROM books
            WHERE id=?"
        );

        $stmt->execute([
            $id,
            $id,
            $id,
            $id
        ]);

        $b = $stmt->fetch();

        if (
            !$b ||
            $b['status'] === 'borrowed' ||
            (int) $b['active_res'] > 0
        ) {
            flash(
                'ลบไม่ได้ หนังสือกำลังถูกยืมหรือมีการจองอยู่',
                'error'
            );
        } elseif (
            (int) $b['all_res'] > 0 ||
            (int) $b['all_rentals'] > 0
        ) {
            flash(
                'ลบไม่ได้ เนื่องจากหนังสือมีประวัติการจองหรือเช่า',
                'error'
            );
        } else {
            $pdo->prepare(
                'DELETE FROM books WHERE id=?'
            )->execute([$id]);

            log_activity(
                'delete',
                'book',
                $id
            );

            flash('ลบหนังสือสำเร็จ');
        }

        redirect('admin/books.php');
    }
}

if (isset($_GET['edit'])) {
    $s = $pdo->prepare(
        'SELECT * FROM books WHERE id=?'
    );

    $s->execute([
        (int) $_GET['edit']
    ]);

    $edit = $s->fetch();
}

$q = trim(
    (string) ($_GET['q'] ?? '')
);

$s = $pdo->prepare(
    'SELECT
        b.*,
        c.name category
    FROM books b
    JOIN categories c
        ON c.id=b.category_id
    WHERE
        b.title LIKE ?
        OR b.author LIKE ?
        OR b.isbn LIKE ?
    ORDER BY b.created_at DESC'
);

$s->execute(
    array_fill(
        0,
        3,
        "%$q%"
    )
);

$books = $s->fetchAll();

$categories = $pdo
    ->query(
        'SELECT *
        FROM categories
        ORDER BY name'
    )
    ->fetchAll();

page_start(
    'จัดการหนังสือ',
    'books'
);
?>

<div class="page-head">
    <div>
        <h1>จัดการหนังสือ</h1>
        <p>
            เพิ่ม แก้ไข และตรวจสอบสถานะหนังสือในร้าน
        </p>
    </div>

    <a
        class="btn btn-primary"
        href="books.php?edit=0"
    >
        + เพิ่มหนังสือ
    </a>
</div>

<?php if (isset($_GET['edit'])): ?>

<section class="panel">

    <div class="panel-head">

        <h2>
            <?= $edit
                ? 'แก้ไขหนังสือ'
                : 'เพิ่มหนังสือใหม่'
            ?>
        </h2>

        <a
            class="btn"
            href="books.php"
        >
            ปิด
        </a>

    </div>

    <form
        class="form-grid"
        method="post"
        enctype="multipart/form-data"
        style="padding:20px"
    >

        <?= csrf_field() ?>

        <input
            type="hidden"
            name="action"
            value="save"
        >

        <input
            type="hidden"
            name="id"
            value="<?= e(
                $edit['id'] ?? ''
            ) ?>"
        >

        <input
            type="hidden"
            name="existing_cover"
            value="<?= e(
                $edit['cover_image'] ?? ''
            ) ?>"
        >

        <label class="field">

            ชื่อหนังสือ

            <input
                name="title"
                value="<?= e(
                    $edit['title'] ?? ''
                ) ?>"
                required
            >

        </label>

        <label class="field">

            ผู้แต่ง

            <input
                name="author"
                value="<?= e(
                    $edit['author'] ?? ''
                ) ?>"
                required
            >

        </label>

        <label class="field">

            หมวดหมู่

            <select
                name="category_id"
                required
            >

                <?php foreach (
                    $categories as $c
                ): ?>

                    <option
                        value="<?= (int) $c['id'] ?>"
                        <?= (
                            ($edit['category_id'] ?? 0)
                            == $c['id']
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >
                        <?= e($c['name']) ?>
                    </option>

                <?php endforeach ?>

            </select>

        </label>

        <label class="field">

            ISBN

            <input
                name="isbn"
                value="<?= e(
                    $edit['isbn'] ?? ''
                ) ?>"
            >

        </label>

        <label class="field">

            สำนักพิมพ์

            <input
                name="publisher"
                value="<?= e(
                    $edit['publisher'] ?? ''
                ) ?>"
            >

        </label>

        <label class="field">

            ปีที่พิมพ์

            <input
                name="publication_year"
                type="number"
                min="1000"
                max="<?= date('Y') ?>"
                value="<?= e(
                    $edit['publication_year']
                    ?? ''
                ) ?>"
            >

        </label>

        <label class="field">

            ค่าเช่าต่อวัน (บาท)

            <input
                name="price"
                type="number"
                step="0.01"
                min="0"
                value="<?= e(
                    $edit[
                        'rental_price_per_day'
                    ] ?? ''
                ) ?>"
                required
            >

        </label>

        <label class="field">

            ภาพปก
            (JPG/PNG/WebP ≤ 3MB)

            <input
                name="cover"
                type="file"
                accept="
                    image/jpeg,
                    image/png,
                    image/webp
                "
            >

        </label>

        <label class="field wide">

            เรื่องย่อ

            <textarea
                name="description"
            ><?= e(
                $edit['description']
                ?? ''
            ) ?></textarea>

        </label>

        <div class="wide">

            <button
                class="btn btn-primary"
            >
                บันทึกหนังสือ
            </button>

        </div>

    </form>

</section>

<?php endif ?>

<form class="filters">

    <input
        name="q"
        value="<?= e($q) ?>"
        placeholder="
            ค้นหาชื่อ ผู้แต่ง หรือ ISBN
        "
    >

    <button class="btn">
        ค้นหา
    </button>

</form>

<section class="panel">

<div class="table-wrap">

<table>

<thead>

<tr>
    <th>ปก</th>
    <th>ชื่อหนังสือ</th>
    <th>หมวดหมู่</th>
    <th>ISBN</th>
    <th>ราคา/วัน</th>
    <th>สถานะ</th>
    <th></th>
</tr>

</thead>

<tbody>

<?php foreach ($books as $b): ?>

<tr>

<td>

<?php if ($b['cover_image']): ?>

<img
    src="<?= base_url(
        $b['cover_image']
    ) ?>"
    alt=""
    width="40"
    height="54"
    style="
        object-fit:cover;
        border-radius:4px
    "
>

<?php else: ?>

—

<?php endif ?>

</td>

<td>

<b>
    <?= e($b['title']) ?>
</b>

<br>

<small>
    <?= e($b['author']) ?>
</small>

</td>

<td>
    <?= e($b['category']) ?>
</td>

<td>
    <?= e(
        $b['isbn'] ?: '-'
    ) ?>
</td>

<td>

<?= number_format(
    (float) $b[
        'rental_price_per_day'
    ],
    2
) ?>

บาท

</td>

<td>

<?= status_badge(
    $b['status'],
    book_status_label(
        $b['status']
    )
) ?>

</td>

<td class="actions">

<a
    class="btn"
    href="?edit=<?= (int) $b['id'] ?>"
>
    แก้ไข
</a>

<form method="post">

<?= csrf_field() ?>

<input
    type="hidden"
    name="action"
    value="delete"
>

<input
    type="hidden"
    name="id"
    value="<?= (int) $b['id'] ?>"
>

<button
    class="btn btn-danger"
    data-confirm="
        ยืนยันการลบหนังสือ?
    "
>
    ลบ
</button>

</form>

</td>

</tr>

<?php endforeach ?>

<?php if (!$books): ?>

<tr>
    <td
        colspan="7"
        class="empty"
    >
        ไม่พบหนังสือ
    </td>
</tr>

<?php endif ?>

</tbody>

</table>

</div>

</section>

<?php page_end(); ?>