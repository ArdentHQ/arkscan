import classNames from "classnames";
import { useState } from "react";
import { useTranslation } from "react-i18next";
import SubmitWalletModal from "./SubmitWalletModal";

export default function SubmitWallet() {
    const { t } = useTranslation();

    const [isModalOpen, setIsModalOpen] = useState(false);

    return (
        <div className="mt-6 flex w-full flex-col items-center justify-between space-y-3 rounded-xl bg-theme-primary-100 px-6 py-6 text-center dark:bg-theme-dark-800 sm:flex-row sm:space-y-0 sm:py-2 sm:text-start">
            <span className="space-x-1 font-semibold text-theme-primary-900 dim:text-theme-dark-50 dark:text-white sm:text-lg">
                <span>{t("pages.compatible-wallets.dont_see_a_wallet")}</span>

                <span className="whitespace-nowrap">{t("pages.compatible-wallets.let_us_know")}</span>
            </span>

            <button type="button" className="button-primary w-full sm:w-auto" onClick={() => setIsModalOpen(true)}>
                {t("actions.submit_wallet")}
            </button>

            <SubmitWalletModal
                isOpen={isModalOpen}
                onClose={() => setIsModalOpen(false)}
                close={() => setIsModalOpen(false)}
            />
        </div>
    );
}
