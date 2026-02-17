import TruncateMiddle from "@/Components/General/TruncateMiddle";
import Layout from "@/Layout";
import { Link, router } from "@inertiajs/react";
import GenericErrorImage from "@ui/images/errors/generic.svg?react";
import { useTranslation } from "react-i18next";

export default function NotFound({ id, type }: { id: string; type: "transaction" | "block" | "wallet" }) {
    const { t } = useTranslation();

    const prefix = {
        transaction: t("errors.transaction_id"),
        block: t("errors.block_id"),
        wallet: t("errors.address"),
    }[type];

    let suffix = t("errors.does_not_exist");
    if (type === "wallet") {
        suffix = t("errors.does_not_exist_yet");
    }

    return (
        <Layout className="px-6 mx:px-10 py-8">
            <div className="text-center">
                <div className="mx-auto w-84">
                    <GenericErrorImage className="light-dark-icon h-full" />
                </div>

                {type === "wallet" ? (
                    <>
                        <div className="mt-8 space-x-1 text-center text-lg font-semibold text-theme-secondary-900 dark:text-theme-dark-200">
                            <span>{t("errors.address")}</span>
                            <span className="hidden bg-theme-warning-50 dark:bg-theme-dark-900 dark:text-white sm:inline">
                                {id}
                            </span>
                            <TruncateMiddle
                                length={17}
                                className="bg-theme-warning-50 dark:bg-theme-dark-900 dark:text-white sm:inline sm:hidden"
                            >
                                {id.slice(0, 6)}...{id.slice(-6)}
                            </TruncateMiddle>
                            <span>{t("errors.does_not_exist")}</span>
                        </div>

                        <div className="mt-3 text-center leading-7 text-theme-secondary-900 dark:text-theme-dark-200">
                            {t("errors.wallet_not_found_details")}
                        </div>
                    </>
                ) : (
                    <div className="mt-8 space-x-1 text-center text-lg font-semibold text-theme-secondary-900 dark:text-theme-dark-200">
                        <span>{prefix}</span>
                        <span className="bg-theme-warning-50 dark:bg-theme-dark-900 dark:text-white">{id}</span>
                        <span>{suffix}</span>
                    </div>
                )}

                <div className="mt-8 flex flex-col space-y-3 sm:flex-row sm:justify-center sm:space-x-3 sm:space-y-0">
                    <Link href={route("home")} className="button button-secondary">
                        {t("general.home", { ns: "ui" })}
                    </Link>

                    <Link className="button button-primary" href="" onClick={() => router.reload()}>
                        {t("general.reload")}
                    </Link>
                </div>
            </div>
        </Layout>
    );
}
