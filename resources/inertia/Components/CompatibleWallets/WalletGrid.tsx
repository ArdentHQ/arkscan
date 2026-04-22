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
    return (
        <div className="md-lg:grid-cols-4 grid grid-cols-1 gap-3 sm:grid-cols-2 md:mt-6 md:grid-cols-3 xl:grid-cols-5">
            {wallets.map((wallet, index) => (
                <a
                    key={index}
                    href={wallet.url}
                    target="_blank"
                    rel="nofollow noopener noreferrer"
                    className="group border-theme-secondary-300 hover:border-theme-primary-200 hover:bg-theme-primary-50 dark:border-theme-dark-700 dark:bg-theme-dark-900 dark:hover:bg-theme-secondary-800 flex flex-col rounded-xl border bg-white transition hover:cursor-pointer"
                >
                    <div className="mx-2 mt-2 flex items-center justify-center rounded-xl">
                        {WalletIcons[wallet.logo] &&
                            createElement(WalletIcons[wallet.logo], { className: "w-full h-full" })}
                    </div>

                    <div className="mx-6 mt-3 mb-6">
                        <span className="text-theme-primary-600 group-hover:text-theme-primary-700 dark:text-theme-dark-blue-400 dark:group-hover:text-theme-dark-blue-500 inline-flex space-x-1 font-semibold break-words transition">
                            <span>{wallet.title}</span>

                            <div>
                                <ArrowExternalIcon className="text-theme-secondary-500 dark:text-theme-dark-500 relative -top-1 mt-1 ml-0.5 inline h-3 w-3 flex-shrink-0" />
                            </div>
                        </span>
                    </div>
                </a>
            ))}
        </div>
    );
}
