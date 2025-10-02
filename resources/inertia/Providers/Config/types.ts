import { Currencies, IConfigProductivity, INetwork, ISettings } from "@/types";

export interface IConfigContextType {
    currencies?: Currencies;
    network?: INetwork;
    productivity?: IConfigProductivity;
    settings?: ISettings;
}
