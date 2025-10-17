import { Currencies, IConfigArkConnect, IConfigPagination, IConfigProductivity, INetwork, ISettings } from "@/types";

export interface IConfigContextType {
    arkconnect?: IConfigArkConnect;
    currencies?: Currencies;
    network?: INetwork;
    productivity?: IConfigProductivity;
    settings?: ISettings;
    pagination?: IConfigPagination;
}
