import axios from 'axios';

const axiosInstance = () => {
    const instance = axios.create({
        baseURL: '/api',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    });

    instance.interceptors.request.use((config) => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        const token = meta ? meta.getAttribute('content') : '';
        if (token) config.headers['X-CSRF-TOKEN'] = token;
        return config;
    });

    return instance;
};

const api = axiosInstance();

class ApiRequest {
    async get(url) {
        return api.get(url);
    }

    async post(url, data) {
        return api.post(url, data);
    }

    async put(url, data) {
        return api.put(url, data);
    }

    async delete(url) {
        return api.delete(url);
    }
}

export default ApiRequest;
