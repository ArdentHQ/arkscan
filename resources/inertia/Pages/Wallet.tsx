import { Head, router } from "@inertiajs/react";
import { useEffect, useRef } from "react";
import { INetwork, ITransaction, IWallet, Currencies, ISettings, IConfigProductivity, IConfigArkConnect, IConfigPagination } from '@/types';
import { usePageMetadata } from "@/Components/General/Metadata";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import TransactionsTableWrapper from "@/Components/Tables/Desktop/Wallet/Transactions";
import ConfigProvider from "@/Providers/Config/ConfigProvider";
import { IPaginatedResponse } from '../types';
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import Overview from "@/Components/Wallet/Overview/Overview";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import TransactionsMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/Transactions";

const WalletTabsWrapper = ({ transactions }: { transactions: IPaginatedResponse<ITransaction> }) => {
    return (
        <TabsProvider
            defaultSelected="transactions"
            tabs={[
                { text: 'Transactions', value: 'transactions' },
                { text: 'Validated Blocks', value: 'blocks' },
                { text: 'Voters', value: 'voters' },
            ]}
        >
            <WalletTabs transactions={transactions} />
        </TabsProvider>
    )
}

const WalletTabs = ({ transactions }: { transactions: IPaginatedResponse<ITransaction> }) => {
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
                    if (callback) {
                        callback();
                    }
                },
            });
        };

        pollCurrentTab();

        setRefreshPage((callback?: CallableFunction) => {
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
                <>
                    <TransactionsTableWrapper
                        transactions={transactions}
                        mobile={<TransactionsMobileTableWrapper transactions={transactions} />}
                    />
                </>
            )}
        </>
    )
}

export default function Wallet({
    arkconnect,
    currencies,
    network,
    productivity,
    settings,
    pagination,
    transactions,
    wallet,
}: {
    arkconnect: IConfigArkConnect;
    currencies: Currencies;
    network: INetwork;
    productivity: IConfigProductivity;
    settings: ISettings;
    pagination: IConfigPagination;
    transactions: IPaginatedResponse<ITransaction>;
    wallet: IWallet;
}) {
    const metadata = usePageMetadata({ page: "wallet", detail: {
        name: network.name,
        address: wallet.address,
    } });

    return (<>
        <Head>{metadata}</Head>

        <ConfigProvider
            arkconnect={arkconnect}
            currencies={currencies}
            productivity={productivity}
            network={network}
            settings={settings}
            pagination={pagination}
        >
            <Overview wallet={wallet} />

            <PageHandlerProvider>
                <WalletTabsWrapper transactions={transactions} />
            </PageHandlerProvider>
        </ConfigProvider>
    </>);
}
