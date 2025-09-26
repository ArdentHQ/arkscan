import { ITransaction } from "@/types";
import Badge from "../General/Badge";
import Tippy from "@tippyjs/react";
import { useTranslation } from "react-i18next";

export default function Method({ transaction }: { transaction: ITransaction }) {
    const { t } = useTranslation();

    if (transaction.isVote) {
        if (transaction.votedFor) {
            return (
                <Tippy content={t('general.transaction.vote_validator', {validator: transaction.votedFor})}>
                    <Badge className="encapsulated-badge">Vote</Badge>
                </Tippy>
            );
        }
        return (
            <Badge className="encapsulated-badge">Vote</Badge>
        );
    }

    return (
        <Badge className="encapsulated-badge">{transaction.type}</Badge>
    );
}
