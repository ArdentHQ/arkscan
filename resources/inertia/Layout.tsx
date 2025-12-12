import Navbar from "./Components/General/Navbar/Navbar";

const Layout = ({ children }: { children: React.ReactNode }) => {
    return (
        <>
            <Navbar />

            <div className="py-8">{children}</div>
        </>
    );
};

export default Layout;
