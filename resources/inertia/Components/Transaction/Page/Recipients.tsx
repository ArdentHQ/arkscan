import { useTranslation } from "react-i18next";
import { PageSection } from "@/Components/PageSection";
import AmountSmall from "@/Components/General/AmountSmall";
import { TransactionRecipient } from "@/Pages/Transaction.contracts";
import { weiToArk } from "@/utils/UnitConverter";
import TransactionAddress from "./Address";

export default function TransactionRecipients({ recipients }: { recipients: TransactionRecipient[] }) {
    const { t } = useTranslation();

    return (
        <PageSection title={t("general.recipients")} noBorder>
            <div className="overflow-hidden rounded-t-xl rounded-b-xl border border-theme-secondary-300 dark:border-theme-dark-700">
                <div className="table-container table-encapsulated encapsulated-table-header-gradient px-6">
                    <table>
                        <thead className="bg-theme-secondary-100 dark:bg-black">
                            <tr className="border-b-none text-sm font-semibold">
                                <th className="w-full py-3 text-left">{t("general.address")}</th>
                                <th className="py-3 text-right">{t("general.transaction.amount")}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {recipients.map((recipient) => (
                                <tr className="text-sm font-semibold" key={`${recipient.address}-${recipient.amount}`}>
                                    <td className="py-3">
                                        <TransactionAddress address={recipient.address} />
                                    </td>
                                    <td className="py-3 text-right">
                                        <AmountSmall amount={Number(weiToArk(recipient.amount))} />
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </PageSection>
    );
}
