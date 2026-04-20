import { IPaginatedResponse } from "@/types";
import { ITokenAction } from "@/types/generated";
import { PageProps } from "@inertiajs/core";

export interface TokenActionsProps
    extends PageProps<{
        transfers?: IPaginatedResponse<ITokenAction>;
    }> {}
