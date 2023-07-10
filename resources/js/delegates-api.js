import axios from "axios";

export class DelegatesApi {
    static async request(host, publicKey) {
        const response = await axios.get(`${host}/delegates/${publicKey}`);

        return response.data;
    }

    static async fetch({ host, publicKey }) {
        const page = await this.request(host, publicKey);

        return page.data;
    }
}
