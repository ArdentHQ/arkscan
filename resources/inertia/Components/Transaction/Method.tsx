import Badge from "../General/Badge";
import { useTranslation } from "react-i18next";
import Tooltip from "../General/Tooltip";
import { Transaction } from "@/models/Transaction";

export default function Method({ transaction }: { transaction: Transaction }) {
    const { t, i18n } = useTranslation();

    if (transaction.method.isVote) {
        if (transaction.votedFor) {
            return (
                <Tooltip
                    content={t("general.transaction.vote_validator", {
                        validator: transaction.votedFor,
                    })}
                >
                    <Badge className="encapsulated-badge">Vote</Badge>
                </Tooltip>
            );
        }
    }

    if (transaction.method.isRevoke) {
        return <Badge className="encapsulated-badge">{t("general.transaction.types.revoke")}</Badge>;
    }

    return <Badge className="encapsulated-badge">{transaction.method.name({ t, i18n })}</Badge>;
}
