import React from'react';import{createRoot}from'react-dom/client';import{BrowserRouter}from'react-router-dom';import App from'./App';import'./styles.css';
const redirect=sessionStorage.redirect;if(redirect){delete sessionStorage.redirect;history.replaceState(null,'',new URL(redirect).pathname)}
createRoot(document.getElementById('root')!).render(<React.StrictMode><BrowserRouter basename={import.meta.env.BASE_URL}><App/></BrowserRouter></React.StrictMode>);
