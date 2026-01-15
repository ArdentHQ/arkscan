export interface CompatibleWallet {
    title: string;
    url: string;
    logo: string;
}

export interface CompatibleWalletsProps {
    wallets: CompatibleWallet[];
}
