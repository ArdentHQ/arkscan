import PageHeaderValuePopup from "./ValuePopup";
import { IWallet } from "@/types/generated";
import { useTranslation } from "react-i18next";
import KeyIcon from "@ui/icons/key.svg?react";

export default function PageHeaderPublicKeyAction({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();

    return (
        <PageHeaderValuePopup
            value={wallet.public_key ?? ''}
            button={<KeyIcon className="w-4 h-4" />}
            title={t('pages.wallet.public_key.title')}
            id="public_key"
        />
    )
}
