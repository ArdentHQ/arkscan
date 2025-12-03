import Navbar from "./Components/General/Navbar/Navbar";

const Layout = ({ children }: { children: React.ReactNode }) => {
    return (
        <>
            <Navbar />

            {children}
        </>
    );
};

export default Layout;
