import { ITransaction } from "@/types/generated";
import Badge from "../General/Badge";
import { useTranslation } from "react-i18next";
import Tooltip from "../General/Tooltip";

export default function Method({ transaction }: { transaction: ITransaction }) {
    const { t } = useTranslation();

    if (transaction.isVote) {
        if (transaction.votedFor) {
            return (
                <Tooltip
                    content={t("general.transaction.vote_validator", {
                        validator: transaction.votedFor,
                    })}
                >
                    <Badge className="encapsulated-badge inline-block">Vote</Badge>
                </Tooltip>
            );
        }
    }

    return <Badge className="encapsulated-badge inline-block">{transaction.type}</Badge>;
}
