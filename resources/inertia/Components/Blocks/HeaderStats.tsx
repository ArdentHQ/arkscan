import Card from "@/Components/General/Card";
import Detail from "@/Components/General/Detail";
import Number from "@/Components/General/Number";
import { useTranslation } from "react-i18next";
import useShareData from "@/hooks/use-shared-data";

import { IBlocksStatistics } from "@/Pages/Blocks.contracts";
import { currency } from "@/utils/number-formatter";

export default function HeaderStats({ statistics }: { statistics: IBlocksStatistics }) {
    const { t } = useTranslation();
    const { network } = useShareData();

    return (
        <div className="flex flex-col space-y-2 px-6 pb-6 sm:space-y-3 md:mx-auto md:max-w-7xl md:px-10 xl:flex-row xl:space-x-3 xl:space-y-0">
            <div className="grid w-full flex-1 grid-cols-1 gap-2 sm:grid-cols-2 md:gap-3 xl:grid-cols-4">
                <Card>
                    <Detail title={t("pages.blocks.blocks_produced_24h")}>
                        <Number>{statistics.forgedCount}</Number>
                    </Detail>
                </Card>

                <Card>
                    <Detail title={t("pages.blocks.missed_blocks_24h")}>
                        <Number>{statistics.missedCount}</Number>
                    </Detail>
                </Card>

                <Card>
                    <Detail title={t("pages.blocks.block_rewards_24h")}>
                        {currency(statistics.totalRewards, network.currency)}
                    </Detail>
                </Card>

                <Card>
                    <Detail title={t("pages.blocks.max_transactions_24h")}>
                        <Number>{statistics.maxTransactions}</Number>
                    </Detail>
                </Card>
            </div>
        </div>
    );
}
