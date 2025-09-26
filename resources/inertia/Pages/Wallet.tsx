import { Head, router } from "@inertiajs/react";
import { useEffect, useRef } from "react";
import { INetwork, ITransaction, IWallet } from "@/types";
import { usePageMetadata } from "@/Components/General/Metadata";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import TransactionsTableWrapper from "@/Components/Tables/Desktop/Wallet/Transactions";
import NetworkProvider from "@/Providers/Network/NetworkProvider";
import { IPaginatedResponse } from '../types';
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";

const WalletTabsWrapper = ({
    transactions,
    network,
}: {
    transactions: IPaginatedResponse<ITransaction>;
    network: INetwork;
}) => {
    return (
        <TabsProvider
            defaultSelected="transactions"
            tabs={[
                { text: 'Transactions', value: 'transactions' },
                { text: 'Validated Blocks', value: 'blocks' },
                { text: 'Voters', value: 'voters' },
            ]}
        >
            <WalletTabs
                transactions={transactions}
                network={network}
            />
        </TabsProvider>
    )
}

const WalletTabs = ({
    transactions,
    network,
}: {
    transactions: IPaginatedResponse<ITransaction>;
    network: INetwork;
}) => {
    const pollingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const { setRefreshPage } = usePageHandler();

    const { currentTab } = useTabs();

    useEffect(() => {
        router.on('success', () => {
            pollingTimerRef.current = setTimeout(pollCurrentTab, 8000);
        });

        const pollCurrentTab = (callback?: CallableFunction) => {
            let pollParameters: string[] = [];
            if (currentTab === 'transactions') {
                pollParameters = [
                    'transactions',
                ];
            } else if (currentTab === 'blocks') {
                pollParameters = [
                    'blocks',
                ];
            } else if (currentTab === 'voting') {
                pollParameters = [
                    'votes',
                ];
            }

            router.reload({
                only: pollParameters,
                onSuccess: () => {
                    console.log('router callback');
                    if (callback) {
                        callback();
                    }
                },
            });
        };

        // pollingTimerRef.current = setTimeout(pollCurrentTab, 8000);
        pollCurrentTab();

        setRefreshPage((callback?: CallableFunction) => {
            console.log('setRefreshPage callback', callback);
            pollCurrentTab(callback);
        });

        return () => {
            if (! pollingTimerRef.current) {
                return;
            }

            clearTimeout(pollingTimerRef.current);
        }
    }, []);

    return (
        <>
            {currentTab === 'transactions' && (
                <TransactionsTableWrapper
                    transactions={transactions}
                    totalCount={transactions?.total}
                    network={network}
                />
            )}
        </>
    )
}

export default function Wallet({ wallet, transactions, network }: {
    wallet: IWallet;
    transactions: IPaginatedResponse<ITransaction>;
    network: INetwork;
}) {
    console.log(transactions);
    const metadata = usePageMetadata({ page: "wallet", detail: {
        name: network.name,
        address: wallet.address,
    } });

    return (<>
        <Head>{metadata}</Head>

        {/* <PageHeader
            title={t('pages.wallet.title')}
            subtitle={t('pages.wallet.subtitle')}
        /> */}

        <NetworkProvider network={network}>
            <PageHandlerProvider>
                <WalletTabsWrapper
                    transactions={transactions}
                    network={network}
                />
            </PageHandlerProvider>
        </NetworkProvider>
    </>);
}
