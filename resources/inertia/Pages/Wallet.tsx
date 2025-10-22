import { Head } from "@inertiajs/react";
import { INetwork, Currencies, ISettings, IConfigProductivity, IConfigArkConnect } from "@/types";
import { IWallet } from "@/types/generated";
import { usePageMetadata } from "@/Components/General/Metadata";
import ConfigProvider from "@/Providers/Config/ConfigProvider";
import Overview from "@/Components/Wallet/Overview/Overview";

export default function Wallet({
	arkconnect,
	currencies,
	network,
	productivity,
	settings,
	wallet,
}: {
	arkconnect: IConfigArkConnect;
	currencies: Currencies;
	network: INetwork;
	productivity: IConfigProductivity;
	settings: ISettings;
	wallet: IWallet;
}) {
	const metadata = usePageMetadata({
		page: "wallet",
		detail: {
			name: network.name,
			address: wallet.address,
		},
	});

	return (
		<>
			<Head>{metadata}</Head>

			<ConfigProvider
				arkconnect={arkconnect}
				currencies={currencies}
				productivity={productivity}
				network={network}
				settings={settings}
			>
				<Overview wallet={wallet} />
			</ConfigProvider>
		</>
	);
}
