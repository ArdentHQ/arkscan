import Key from "@/Assets/Icons/Key";
import PageHeaderValuePopup from "./ValuePopup";
import { IWallet } from "@/types";
import { useTranslation } from "react-i18next";

export default function PageHeaderPublicKeyAction({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();

    return (
        <PageHeaderValuePopup
            value={wallet.public_key}
            button={<Key className="w-4 h-4" />}
            title={t('pages.wallet.public_key.title')}
            id="public_key"
        />
    )
}
