import axios from "axios";

export class ValidatorsApi {
    static async request(host: string, publicKey: string) {
        const response = await axios.get(`${host}/delegates/${publicKey}`);

        return response.data;
    }

    static async fetch({ host, publicKey }: { host: string; publicKey: string }) {
        const page = await this.request(host, publicKey);

        return page.data;
    }
}
