import { IPaginatedResponse } from "@/types";
import { ITokenTransfer } from "@/types/generated";
import { PageProps } from "@inertiajs/core";

export interface TokenTransfersProps
    extends PageProps<{
        transfers?: IPaginatedResponse<ITokenTransfer>;
    }> {}
