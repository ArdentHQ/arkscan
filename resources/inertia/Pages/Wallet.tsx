import { Head } from "@inertiajs/react";
import { IWallet } from "@/types/generated";
import { usePageMetadata } from "@/Components/General/Metadata";
import ConfigProvider from "@/Providers/Config/ConfigProvider";
import Overview from "@/Components/Wallet/Overview/Overview";
import { PageProps } from "@inertiajs/core";

export default function Wallet({
    wallet,
    network,
    ...props
}: PageProps<{
    wallet: IWallet;
}>) {
    console.log({ network, wallet, ...props });
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

            <ConfigProvider network={network} {...props}>
                <Overview wallet={wallet} />
            </ConfigProvider>
        </>
    );
}
