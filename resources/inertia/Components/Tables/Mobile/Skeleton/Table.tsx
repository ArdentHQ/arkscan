import MobileTable from "../Table";

export default function LoadingTable({ children }: { children: React.ReactNode }) {
    return <MobileTable className="md:hidden">{children}</MobileTable>;
}
