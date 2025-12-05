class ApiRequest {
    constructor() {  
        this.API_BASE_URL = '/api';
    }
    
    getHeaders() {
        let token = document.querySelector('meta[name="csrf-token"]');
        token = token ? token.getAttribute('content') : '';

        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
        };
    }

    async get(url) {
        const res = await fetch(`${this.API_BASE_URL}${url}`, {
            method: 'GET',
            headers: this.getHeaders(),
        });

        return res;
    }   

    async post(url, data) {
        const res = await fetch(`${this.API_BASE_URL}${url}`, {
            method: 'POST',
            headers: this.getHeaders(),
            body: JSON.stringify(data),
        });

        return res;
    }

    async put(url, data) {
        const res = await fetch(`${this.API_BASE_URL}${url}`, {
            method: 'PUT',
            headers: this.getHeaders(),
            body: JSON.stringify(data),
        });

        return res;
    }

    async delete(url) {
        const res = await fetch(`${this.API_BASE_URL}${url}`, {
            method: 'DELETE',
            headers: this.getHeaders(),
        });

        return res;
    }
}

export default ApiRequest;