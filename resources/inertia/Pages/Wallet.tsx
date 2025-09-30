import { Head } from "@inertiajs/react";
import { INetwork, IWallet, Currencies, ISettings, IConfigProductivity } from '@/types';
import { usePageMetadata } from "@/Components/General/Metadata";
import ConfigProvider from "@/Providers/Config/ConfigProvider";
import Overview from "@/Components/Wallet/Overview/Overview";

export default function Wallet({
    currencies,
    network,
    productivity,
    settings,
    wallet,
}: {
    currencies: Currencies;
    network: INetwork;
    productivity: IConfigProductivity;
    settings: ISettings;
    wallet: IWallet;
}) {
    const metadata = usePageMetadata({ page: "wallet", detail: {
        name: network.name,
        address: wallet.address,
    } });

    return (<>
        <Head>{metadata}</Head>

        <ConfigProvider
            currencies={currencies}
            productivity={productivity}
            network={network}
            settings={settings}
        >
            <Overview wallet={wallet} />
        </ConfigProvider>
    </>);
}
