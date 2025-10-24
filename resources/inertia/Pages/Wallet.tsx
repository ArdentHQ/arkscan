import { PageProps } from "@inertiajs/core";
import { Head, router } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import { IPaginatedResponse } from "@/types";
import { IWallet, ITransaction } from "@/types/generated";
import { usePageMetadata } from "@/Components/General/Metadata";
import TabsProvider from "@/Providers/Tabs/TabsProvider";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import TransactionsTableWrapper from "@/Components/Tables/Desktop/Wallet/Transactions";
import ConfigProvider from "@/Providers/Config/ConfigProvider";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import Overview from "@/Components/Wallet/Overview/Overview";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import TransactionsMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/Transactions";
import Modal from "@/Components/General/Modal";

const WalletTabsWrapper = ({ transactions }: { transactions: IPaginatedResponse<ITransaction> }) => {
    return (
        <TabsProvider
            defaultSelected="transactions"
            tabs={[
                { text: "Transactions", value: "transactions" },
                { text: "Validated Blocks", value: "blocks" },
                { text: "Voters", value: "voters" },
            ]}
        >
            <WalletTabs transactions={transactions} />
        </TabsProvider>
    );
};

const WalletTabs = ({ transactions }: { transactions: IPaginatedResponse<ITransaction> }) => {
    const pollingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const { setRefreshPage } = usePageHandler();

    const { currentTab } = useTabs();

    useEffect(() => {
        router.on("success", () => {
            pollingTimerRef.current = setTimeout(pollCurrentTab, 8000);
        });

        const pollCurrentTab = (callback?: CallableFunction) => {
            let pollParameters: string[] = [];
            if (currentTab === "transactions") {
                pollParameters = ["transactions"];
            } else if (currentTab === "blocks") {
                pollParameters = ["blocks"];
            } else if (currentTab === "voting") {
                pollParameters = ["votes"];
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
            if (!pollingTimerRef.current) {
                return;
            }

            clearTimeout(pollingTimerRef.current);
        };
    }, []);

    return (
        <>
            {currentTab === "transactions" && (
                <>
                    <TransactionsTableWrapper
                        transactions={transactions}
                        mobile={<TransactionsMobileTableWrapper transactions={transactions} />}
                    />
                </>
            )}
        </>
    );
};

export default function Wallet({
    transactions,
    wallet,
    network,
    ...props
}: PageProps<{
    transactions: IPaginatedResponse<ITransaction>;
    wallet: IWallet;
}>) {
    const metadata = usePageMetadata({
        page: "wallet",
        detail: {
            name: network.name,
            address: wallet.address,
        },
    });

    const [isModalOpen, setIsModalOpen] = useState(true);

    return (
        <>
            <Head>{metadata}</Head>

            <ConfigProvider network={network} {...props}>
                <button onClick={() => setIsModalOpen(true)}>Open Modal</button>

                <Modal
                    isOpen={isModalOpen}
                    onClose={() => setIsModalOpen(false)}
                    title="Export Table"
                    footer={
                        <div className="modal-buttons flex">
                            <button type="button" className="button-secondary">
                                Cancel{" "}
                            </button>

                            <button
                                type="button"
                                className="button-primary flex items-center justify-center space-x-2 sm:mb-0 sm:px-4 sm:py-1.5"
                                disabled={false}
                            >
                                <span>Export</span>
                            </button>
                        </div>
                    }
                >
                    <p>
                        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error ipsum repellat odit fuga, atque
                        dolorem unde minus quas tenetur iure? Veritatis iusto accusantium vel! Officiis laudantium odit
                        ullam enim autem.m
                    </p>
                    <p>
                        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error ipsum repellat odit fuga, atque
                        dolorem unde minus quas tenetur iure? Veritatis iusto accusantium vel! Officiis laudantium odit
                        ullam enim autem.m
                    </p>
                    <p>
                        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error ipsum repellat odit fuga, atque
                        dolorem unde minus quas tenetur iure? Veritatis iusto accusantium vel! Officiis laudantium odit
                        ullam enim autem.m
                    </p>
                    <p>
                        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error ipsum repellat odit fuga, atque
                        dolorem unde minus quas tenetur iure? Veritatis iusto accusantium vel! Officiis laudantium odit
                        ullam enim autem.m
                    </p>
                </Modal>

                <Overview wallet={wallet} />

                <PageHandlerProvider>
                    <WalletTabsWrapper transactions={transactions} />
                </PageHandlerProvider>
            </ConfigProvider>
        </>
    );
}
