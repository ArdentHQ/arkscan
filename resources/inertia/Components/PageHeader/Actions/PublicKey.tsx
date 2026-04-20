import PageHeaderValuePopup from "./ValuePopup";
import { IWallet } from "@/types/generated";
import { useTranslation } from "react-i18next";
import KeyIcon from "@ui/icons/key.svg?react";

export default function PageHeaderPublicKeyAction({ wallet, testId }: { wallet: IWallet; testId?: string }) {
    const { t } = useTranslation();

    return (
        <PageHeaderValuePopup
            value={wallet.public_key ?? ""}
            button={<KeyIcon className="h-4 w-4" />}
            title={t("pages.wallet.public_key.title")}
            id="public_key"
            copiedTooltip={t("pages.wallet.copied_public_key")}
            testId={testId}
        />
    );
}
