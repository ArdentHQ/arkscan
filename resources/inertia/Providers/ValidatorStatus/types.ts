import { IValidator } from "@/types";
import { Dayjs } from "dayjs";

export interface IValidatorStatusContextType {
    output: string;
    dateTime: Dayjs;
    status: ForgingStatus;
    validator: IValidator;
    seconds: number;
}

export type ForgingStatus = "generated" | "missed" | "pending" | "generating";

export const ForgingStatusGenerated: ForgingStatus = "generated";
export const ForgingStatusMissed: ForgingStatus = "missed";
export const ForgingStatusPending: ForgingStatus = "pending";
export const ForgingStatusGenerating: ForgingStatus = "generating";
