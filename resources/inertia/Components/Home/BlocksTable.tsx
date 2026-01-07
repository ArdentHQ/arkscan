import { IPaginatedResponse } from "@/types";
import { IBlock } from "@/types/generated";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { BlocksListLoadingState, BlocksListTable } from "../Tables/Desktop/Blocks/List";
import ViewAllFooter from "./ViewAllFooter";
import BlocksListMobileTableWrapper from "../Tables/Mobile/Blocks/List";
import { MobileBlocksListSkeletonTable } from "../Tables/Mobile/Skeleton/Blocks/List";

export default function HomeBlocksTableWrapper({ blocks }: { blocks?: IPaginatedResponse<IBlock> }) {
    const { t } = useTranslation();
    const { pagination } = useSharedData();
    const rowCount = blocks?.per_page ?? pagination?.per_page ?? 25;

    if (!blocks) {
        return (
            <BlocksListLoadingState
                mobile={<MobileBlocksListSkeletonTable rowCount={rowCount} />}
                rowCount={rowCount}
            />
        );
    }

    return (
        <>
            <BlocksListTable
                blocks={blocks}
                mobile={<BlocksListMobileTableWrapper withResultCount={false} />}
                withHeader={false}
                hidePagination={true}
            />

            <div className="px-6 md:mx-auto md:max-w-7xl md:px-10">
                <ViewAllFooter
                    total={blocks.total ?? 0}
                    suffix={t("tables.home.blocks")}
                    href={route("blocks", { page: 2 })}
                />
            </div>
        </>
    );
}
