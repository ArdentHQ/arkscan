export class ValidatorsApi {
    static async request(host: string, publicKey: string) {
        const response = await fetch(`${host}/delegates/${publicKey}`);

        if (!response.ok) {
            throw new Error(`Request failed with status ${response.status}`);
        }

        return response.json();
    }

    static async fetch({ host, publicKey }: { host: string; publicKey: string }) {
        const page = await this.request(host, publicKey);

        return page.data;
    }
}
