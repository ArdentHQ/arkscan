import ArrowExternalIcon from "@ui/icons/arrows/arrow-external.svg?react";
import AbraWalletIcon from "@icons/wallets/abra.svg?react";
import ArkConnectIcon from "@icons/wallets/arkconnect.svg?react";
import AtomicIcon from "@icons/wallets/atomic.svg?react";
import CryptoIcon from "@icons/wallets/crypto.svg?react";
import ExodusIcon from "@icons/wallets/exodus.svg?react";
import LedgerIcon from "@icons/wallets/ledger.svg?react";
import { createElement } from "react";
import { CompatibleWallet } from "@/Pages/CompatibleWallets.contracts";

const WalletIcons: Record<string, React.FC<React.SVGProps<SVGSVGElement>>> = {
    abra: AbraWalletIcon,
    arkconnect: ArkConnectIcon,
    atomic: AtomicIcon,
    crypto: CryptoIcon,
    exodus: ExodusIcon,
    ledger: LedgerIcon,
};

export default function WalletGrid({ wallets }: { wallets: CompatibleWallet[] }) {
    console.log(wallets);
    return (
        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2 md:mt-6 md:grid-cols-3 md-lg:grid-cols-4 xl:grid-cols-5">
            {wallets.map((wallet, index) => (
                <a
                    key={index}
                    href={wallet.url}
                    target="_blank"
                    rel="nofollow noopener noreferrer"
                    className="group flex flex-col rounded-xl border border-theme-secondary-300 bg-white transition hover:cursor-pointer hover:border-theme-primary-200 hover:bg-theme-primary-50 dark:border-theme-dark-700 dark:bg-theme-dark-900 dark:hover:bg-theme-secondary-800"
                >
                    <div className="mx-2 mt-2 flex items-center justify-center rounded-xl">
                        {WalletIcons[wallet.logo] &&
                            createElement(WalletIcons[wallet.logo], { className: "w-full h-full" })}
                    </div>

                    <div className="mx-6 mb-6 mt-3">
                        <span className="inline-flex space-x-1 break-words font-semibold text-theme-primary-600 transition group-hover:text-theme-primary-700 dark:text-theme-dark-blue-400 dark:group-hover:text-theme-dark-blue-500">
                            <span>{wallet.title}</span>

                            <div>
                                <ArrowExternalIcon className="relative -top-1 ml-0.5 mt-1 inline h-3 w-3 flex-shrink-0 text-theme-secondary-500 dark:text-theme-dark-500" />
                            </div>
                        </span>
                    </div>
                </a>
            ))}
        </div>
    );
}
