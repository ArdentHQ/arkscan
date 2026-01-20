import { IExchange } from '../types/generated';
import { IPaginatedResponse } from '../types';

export interface ExchangeDropdownItem {
    title: string;
    value: string;
}
export interface ExchangesProps {
    typeOptions: ExchangeDropdownItem[];
    pairOptions: ExchangeDropdownItem[];
    exchanges: IPaginatedResponse<IExchange>;
}
