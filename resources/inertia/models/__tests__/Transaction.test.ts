jest.mock("react-i18next", () => ({
    useTranslation: () => ({
        t: (key: string) => key,
        i18n: { exists: () => false },
    }),
}));

jest.mock("@arkecosystem/typescript-crypto", () => ({
    TransactionTypeIdentifier: {
        isTokenTransfer: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("a9059cbb"),
        isMultiPayment: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("1234abcd"),
        isVote: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("5678ef01"),
        isUnvote: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("9abcdef0"),
        isValidatorRegistration: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("11223344"),
        isValidatorResignation: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("55667788"),
        isUpdateValidator: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("99aabbcc"),
        isUsernameRegistration: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("ddeeff00"),
        isUsernameResignation: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("ff112233"),
        isApprove: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("095ea7b3"),
        isRevoke: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("9faf57c0"),
        isBatchTransfer: (raw: string) => raw.toLowerCase().replace(/^0x/, "").startsWith("33333333"),
    },
    UnitConverter: {
        formatUnits: (value: string, _unit: string) => Number(value) / 1e8,
    },
}));

import { Transaction } from "../Transaction";
import { CONTRACT_METHODS, makeNetwork, makeTransaction, makeWallet } from "./factories";

const network = makeNetwork();

describe("Transaction", () => {
    describe("make", () => {
        it("creates a Transaction instance from raw data", () => {
            const tx = Transaction.make(makeTransaction(), network);

            expect(tx).toBeInstanceOf(Transaction);
        });

        it("assigns all ITransaction properties to the instance", () => {
            const data = makeTransaction({ hash: "0xdeadbeef" });
            const tx = Transaction.make(data, network);

            expect(tx.hash).toBe("0xdeadbeef");
        });

        it("wraps validatorRegistration as a Transaction when present", () => {
            const registration = makeTransaction({ hash: "0xreg123" });
            const tx = Transaction.make(makeTransaction({ validatorRegistration: registration }), network);

            expect(tx.validatorRegistration).toBeInstanceOf(Transaction);
            expect(tx.validatorRegistration!.hash).toBe("0xreg123");
        });

        it("leaves validatorRegistration as null when absent", () => {
            const tx = Transaction.make(makeTransaction({ validatorRegistration: null }), network);

            expect(tx.validatorRegistration).toBeNull();
        });
    });

    describe("fromArray", () => {
        it("returns an array of Transaction instances", () => {
            const txs = Transaction.fromArray(
                [makeTransaction({ hash: "0x1" }), makeTransaction({ hash: "0x2" })],
                network,
            );

            expect(txs).toHaveLength(2);
            expect(txs[0]).toBeInstanceOf(Transaction);
            expect(txs[1]).toBeInstanceOf(Transaction);
            expect(txs[0].hash).toBe("0x1");
            expect(txs[1].hash).toBe("0x2");
        });

        it("returns an empty array when given an empty array", () => {
            expect(Transaction.fromArray([], network)).toEqual([]);
        });
    });

    describe("hasFailed", () => {
        it("returns true when status is false", () => {
            const tx = Transaction.make(makeTransaction({ status: false }), network);

            expect(tx.hasFailed).toBe(true);
        });

        it("returns false when status is true", () => {
            const tx = Transaction.make(makeTransaction({ status: true }), network);

            expect(tx.hasFailed).toBe(false);
        });
    });

    describe("isSent", () => {
        it("returns true when the address matches the from field", () => {
            const tx = Transaction.make(makeTransaction({ from: "my-address" }), network);

            expect(tx.isSent("my-address")).toBe(true);
        });

        it("returns false when the address does not match", () => {
            const tx = Transaction.make(makeTransaction({ from: "my-address" }), network);

            expect(tx.isSent("other-address")).toBe(false);
        });
    });

    describe("isReceived", () => {
        beforeEach(() => {
            jest.spyOn(console, "log").mockImplementation(() => {});
        });

        afterEach(() => {
            jest.restoreAllMocks();
        });

        it("returns true when the to address matches", () => {
            const tx = Transaction.make(makeTransaction({ to: "recipient-address" }), network);

            expect(tx.isReceived("recipient-address")).toBe(true);
        });

        it("returns false when the to address does not match", () => {
            const tx = Transaction.make(makeTransaction({ to: "recipient-address" }), network);

            expect(tx.isReceived("other-address")).toBe(false);
        });

        it("returns false when to is null", () => {
            const tx = Transaction.make(makeTransaction({ to: null }), network);

            expect(tx.isReceived("some-address")).toBe(false);
        });
    });

    describe("isSentToSelf", () => {
        const senderAddress = "sender-address";

        it("returns true for a regular transfer sent to the sender's own address", () => {
            const tx = Transaction.make(
                makeTransaction({
                    from: senderAddress,
                    to: senderAddress,
                    methodData: { functionName: null, methodId: null, arguments: [] },
                }),
                network,
            );

            expect(tx.isSentToSelf(senderAddress)).toBe(true);
        });

        it("returns false for a regular transfer when sender address does not match", () => {
            const tx = Transaction.make(
                makeTransaction({
                    from: senderAddress,
                    to: senderAddress,
                    methodData: { functionName: null, methodId: null, arguments: [] },
                }),
                network,
            );

            expect(tx.isSentToSelf("other-address")).toBe(false);
        });

        it("returns false for a regular transfer when to address differs from sender", () => {
            const tx = Transaction.make(
                makeTransaction({
                    from: senderAddress,
                    to: "someone-else",
                    methodData: { functionName: null, methodId: null, arguments: [] },
                }),
                network,
            );

            expect(tx.isSentToSelf(senderAddress)).toBe(false);
        });

        it("returns true for a token transfer sent to the sender's own address", () => {
            const tx = Transaction.make(
                makeTransaction({
                    from: senderAddress,
                    to: senderAddress,
                    methodData: { functionName: null, methodId: CONTRACT_METHODS.transfer, arguments: [] },
                }),
                network,
            );

            expect(tx.isSentToSelf(senderAddress)).toBe(true);
        });

        it("returns false when the transaction type is neither transfer nor token transfer", () => {
            const tx = Transaction.make(
                makeTransaction({
                    from: senderAddress,
                    to: senderAddress,
                    methodData: { functionName: null, methodId: CONTRACT_METHODS.vote, arguments: [] },
                }),
                network,
            );

            expect(tx.isSentToSelf(senderAddress)).toBe(false);
        });
    });

    describe("amount", () => {
        it("returns the formatted value for a regular transfer", () => {
            // UnitConverter.formatUnits mock divides by 1e8
            // value = '100000000' → amount = 1.0
            const tx = Transaction.make(
                makeTransaction({
                    value: "100000000",
                    methodData: { functionName: null, methodId: null, arguments: [] },
                }),
                network,
            );

            expect(tx.amount).toBe(1.0);
        });

        it("returns the sum of recipient amounts for a multi-payment", () => {
            // toFloat divides by 1e18 by default
            // recipients: 1e18 + 2e18 → toFloat → 1.0 + 2.0 = 3.0
            const tx = Transaction.make(
                makeTransaction({
                    methodData: { functionName: null, methodId: CONTRACT_METHODS.multipayment, arguments: [] },
                    multiPaymentRecipients: [
                        { address: "addr1", amount: "1000000000000000000" },
                        { address: "addr2", amount: "2000000000000000000" },
                    ],
                }),
                network,
            );

            expect(tx.amount).toBe(3.0);
        });

        it("returns 0 for a multi-payment with no recipients", () => {
            const tx = Transaction.make(
                makeTransaction({
                    methodData: { functionName: null, methodId: CONTRACT_METHODS.multipayment, arguments: [] },
                    multiPaymentRecipients: [],
                }),
                network,
            );

            expect(tx.amount).toBe(0);
        });
    });

    describe("amountForItself", () => {
        it("returns 0 for a non-multi-payment transaction", () => {
            const tx = Transaction.make(
                makeTransaction({
                    methodData: { functionName: null, methodId: null, arguments: [] },
                }),
                network,
            );

            expect(tx.amountForItself).toBe(0);
        });

        it("returns the total amount sent to the sender in a multi-payment", () => {
            const sender = makeWallet({ address: "sender-addr" });

            const tx = Transaction.make(
                makeTransaction({
                    methodData: { functionName: null, methodId: CONTRACT_METHODS.multipayment, arguments: [] },
                    sender,
                    multiPaymentRecipients: [
                        { address: "sender-addr", amount: "1000000000000000000" },
                        { address: "other-addr", amount: "2000000000000000000" },
                        { address: "SENDER-ADDR", amount: "500000000000000000" }, // case-insensitive match
                    ],
                }),
                network,
            );

            expect(tx.amountForItself).toBe(1.5);
        });

        it("returns 0 when the sender is null", () => {
            const tx = Transaction.make(
                makeTransaction({
                    methodData: { functionName: null, methodId: CONTRACT_METHODS.multipayment, arguments: [] },
                    sender: null,
                    multiPaymentRecipients: [{ address: "some-addr", amount: "1000000000000000000" }],
                }),
                network,
            );

            expect(tx.amountForItself).toBe(0);
        });
    });

    describe("amountExcludingItself", () => {
        it("returns 0 for a non-multi-payment transaction", () => {
            const tx = Transaction.make(
                makeTransaction({
                    methodData: { functionName: null, methodId: null, arguments: [] },
                }),
                network,
            );

            expect(tx.amountExcludingItself).toBe(0);
        });

        it("returns the total amount sent to non-sender recipients in a multi-payment", () => {
            const sender = makeWallet({ address: "sender-addr" });

            const tx = Transaction.make(
                makeTransaction({
                    methodData: { functionName: null, methodId: CONTRACT_METHODS.multipayment, arguments: [] },
                    sender,
                    multiPaymentRecipients: [
                        { address: "sender-addr", amount: "1000000000000000000" },
                        { address: "other-addr", amount: "2000000000000000000" },
                    ],
                }),
                network,
            );

            expect(tx.amountExcludingItself).toBe(2.0);
        });

        it("returns the full amount when the sender is null", () => {
            const tx = Transaction.make(
                makeTransaction({
                    methodData: { functionName: null, methodId: CONTRACT_METHODS.multipayment, arguments: [] },
                    sender: null,
                    multiPaymentRecipients: [
                        { address: "addr1", amount: "1000000000000000000" },
                        { address: "addr2", amount: "2000000000000000000" },
                    ],
                }),
                network,
            );

            // sender is null so all recipients are considered non-sender
            expect(tx.amountExcludingItself).toBe(3.0);
        });
    });

    describe("amountWithFee", () => {
        it("returns the sum of toFloat(value) and fee", () => {
            // value = '2000000000000000000' → toFloat → 2.0
            // gas_price = '100', gas_used = '100' → gasPrice = 10000 → formatUnits(10000, 'ark') = 10000/1e8 = 0.0001
            const tx = Transaction.make(
                makeTransaction({
                    value: "2000000000000000000",
                    gas_price: "100",
                    gas_used: "100",
                    methodData: { functionName: null, methodId: null, arguments: [] },
                }),
                network,
            );

            expect(tx.amountWithFee).toBeCloseTo(2.0001, 4);
        });
    });

    describe("amountReceived", () => {
        it("returns the full amount when not a multi-payment", () => {
            const tx = Transaction.make(
                makeTransaction({
                    value: "100000000",
                    methodData: { functionName: null, methodId: null, arguments: [] },
                }),
                network,
            );

            expect(tx.amountReceived()).toBe(1.0);
        });

        it("returns the full amount when no address is specified for a multi-payment", () => {
            const tx = Transaction.make(
                makeTransaction({
                    methodData: { functionName: null, methodId: CONTRACT_METHODS.multipayment, arguments: [] },
                    multiPaymentRecipients: [
                        { address: "addr1", amount: "1000000000000000000" },
                        { address: "addr2", amount: "2000000000000000000" },
                    ],
                }),
                network,
            );

            expect(tx.amountReceived()).toBe(3.0);
        });

        it("returns the amount received by a specific address in a multi-payment", () => {
            const tx = Transaction.make(
                makeTransaction({
                    methodData: { functionName: null, methodId: CONTRACT_METHODS.multipayment, arguments: [] },
                    multiPaymentRecipients: [
                        { address: "addr1", amount: "1000000000000000000" },
                        { address: "addr2", amount: "2000000000000000000" },
                        { address: "ADDR1", amount: "500000000000000000" }, // case-insensitive match
                    ],
                }),
                network,
            );

            expect(tx.amountReceived("addr1")).toBe(1.5);
        });

        it("returns 0 when the address is not a recipient in a multi-payment", () => {
            const tx = Transaction.make(
                makeTransaction({
                    methodData: { functionName: null, methodId: CONTRACT_METHODS.multipayment, arguments: [] },
                    multiPaymentRecipients: [{ address: "addr1", amount: "1000000000000000000" }],
                }),
                network,
            );

            expect(tx.amountReceived("addr2")).toBe(0);
        });
    });

    describe("fee", () => {
        it("returns gas_price multiplied by gas_used, formatted via UnitConverter", () => {
            // gas_price = '200', gas_used = '500' → gasPrice = 100000 → 100000/1e8 = 0.001
            const tx = Transaction.make(makeTransaction({ gas_price: "200", gas_used: "500" }), network);

            expect(tx.fee).toBe(0.001);
        });

        it("returns 0 when gas_price or gas_used is 0", () => {
            const tx = Transaction.make(makeTransaction({ gas_price: "0", gas_used: "21000" }), network);

            expect(tx.fee).toBe(0);
        });
    });
});
