import axios from "axios";

export class WalletsApi {
    static async wallet(host, address) {
        const response = await axios.get(`${host}/wallets/${address}`);

        return response.data.data;
    }

    static async getVote(host, address) {
        const wallet = await this.wallet(host, address);

        return wallet?.attributes?.vote;
    }
}
