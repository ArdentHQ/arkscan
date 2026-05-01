export class WalletsApi {
    static async wallet(host: string, address: string) {
        const response = await fetch(`${host}/wallets/${address}`);

        if (!response.ok) {
            throw new Error(`Request failed with status ${response.status}`);
        }

        const payload = await response.json();

        return payload.data;
    }

    static async getVote(host: string, address: string) {
        const wallet = await this.wallet(host, address);

        return wallet?.attributes?.vote;
    }
}
