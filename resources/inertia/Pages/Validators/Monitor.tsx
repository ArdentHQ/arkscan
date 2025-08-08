import { router } from "@inertiajs/react";
import { useEffect, useRef } from "react";
import Address from "../../Components/Wallet/Address";
import Status from "@/Components/Validator/Monitor/Status";
import TimeToForge from "@/Components/Validator/Monitor/TimeToForge";

export function MonitorRow({ validator }) {
    return (
        <tr>
            <td
                className="text-center"
                width={20}
            >
                <span className="inline-block w-4 h-4 rounded-full bg-theme-secondary-300 dark:bg-theme-dark-700"></span>
            </td>

            <td
                className="text-center"
                width={60}
            >
                {validator.order}
            </td>

            <td className="text-left">
                <Address wallet={validator.wallet} />
            </td>

            <td className="table-cell text-left">
                <Status wallet={validator.wallet} />
            </td>

            <td
                className="md:table-cell text-left whitespace-nowrap"
                width={160}
            >
                {/* {validator.forgingAt} */}
                <TimeToForge forgingAt={validator.forgingAt} wallet={validator.wallet} />
            </td>

            <td className="text-right">
                {validator?.lastBlock?.number}
            </td>
        </tr>
    );
}

export default function Monitor({ round, validators, overflowValidators, height, statistics }) {
    const pollingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    useEffect(() => {
        router.on('success', () => {
            // console.log("Polling successful");

            pollingTimerRef.current = setTimeout(pollValidators, 2000);
        });

        const pollValidators = () => {
            // console.log("Polling validators...");

            router.reload();
        };

        pollingTimerRef.current = setTimeout(pollValidators, 2000);

        return () => {
            if (! pollingTimerRef.current) {
                return;
            }

            clearTimeout(pollingTimerRef.current);
        }
    }, []);

    if (validators.length > 0) {
        console.log("Validator:", validators[5]);
        console.log("Validator:", validators[6]);
        console.log("Validator:", validators[7]);
    }

    return (
        <div className="border border-theme-secondary-300 dark:border-theme-dark-700 overflow-hidden rounded-t-xl rounded-b-xl hidden w-full md:block validator-monitor">
            <div className="px-6 table-container table-encapsulated encapsulated-table-header-gradient">
                <table>
                    <thead>
                        <tr>
                            <th className="" sorting-id="header-favorite">
                                <span></span>
                            </th>

                            <th className="" sorting-id="header-order">
                                <span>Order</span>
                            </th>

                            <th className="text-left">
                                Validator
                            </th>

                            <th className="table-cell w-[374px] text-left">
                                <div>Status</div>
                            </th>

                            <th className="table-cell whitespace-nowrap">
                                Time to Forge
                            </th>

                            <th className="text-right whitespace-nowrap">
                                <span>Block Height</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {validators.map((validator, index) => (
                            <MonitorRow key={index} validator={validator} />
                        ))}
                    </tbody>
                </table>

                {overflowValidators.length > 0 && (
                    <table>
                        <tbody>
                            {overflowValidators.map((validator, index) => (
                                <MonitorRow key={index} validator={validator} />
                            ))}
                        </tbody>
                    </table>
                )}
            </div>
        </div>

    );
}
