import './bootstrap';
import Alpine from 'alpinejs';
import axios from 'axios';

// Set CSRF token globally for Axios
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token meta tag not found');
}

window.axios = axios;
window.Alpine = Alpine;

Alpine.start();

axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            alert('Session expired. Please refresh the page and try again.');
            window.location.reload();
        }
        return Promise.reject(error);
    }
);