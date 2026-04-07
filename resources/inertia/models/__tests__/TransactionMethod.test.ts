// var declarations are used so they are accessible inside jest.mock factory closures (which are hoisted)
var mockT: jest.Mock;
var mockI18nExists: jest.Mock;

jest.mock("react-i18next", () => ({
    useTranslation: () => ({
        t: (...args: unknown[]) => mockT(...args),
        i18n: { exists: (...args: unknown[]) => mockI18nExists(...args) },
    }),
}));

jest.mock("@arkecosystem/typescript-crypto", () => {
    const normalize = (raw: string) => raw.toLowerCase().replace(/^0x/, "");
    const isMethod = (raw: string, methodId: string) => normalize(raw).startsWith(methodId.toLowerCase());

    return {
        TransactionTypeIdentifier: {
            isTokenTransfer: (raw: string) => isMethod(raw, "a9059cbb"),
            isMultiPayment: (raw: string) => isMethod(raw, "1234abcd"),
            isVote: (raw: string) => isMethod(raw, "5678ef01"),
            isUnvote: (raw: string) => isMethod(raw, "9abcdef0"),
            isValidatorRegistration: (raw: string) => isMethod(raw, "11223344"),
            isValidatorResignation: (raw: string) => isMethod(raw, "55667788"),
            isUpdateValidator: (raw: string) => isMethod(raw, "99aabbcc"),
            isUsernameRegistration: (raw: string) => isMethod(raw, "ddeeff00"),
            isUsernameResignation: (raw: string) => isMethod(raw, "ff112233"),
            isApprove: (raw: string) => isMethod(raw, "095ea7b3"),
            isRevoke: (raw: string) => isMethod(raw, "9faf57c0"),
            isBatchTransfer: (raw: string) => isMethod(raw, "33333333"),
        },
    };
});

import { TransactionMethod } from "../TransactionMethod";
import { CONTRACT_METHODS, makeMemoryWallet, makeNetwork, makeTransaction } from "./factories";

const network = makeNetwork();

beforeEach(() => {
    mockT = jest.fn((key: string) => key);
    mockI18nExists = jest.fn(() => false);
});

const method = (methodId: string | null, overrides = {}) =>
    new TransactionMethod(
        makeTransaction({
            methodData: { functionName: null, methodId, arguments: [] },
            ...overrides,
        }),
        network,
    );

describe("TransactionMethod", () => {
    describe("isTransfer", () => {
        it("returns true when methodHash is null", () => {
            expect(method(null).isTransfer).toBe(true);
        });

        it("returns false when methodHash is not null", () => {
            expect(method(CONTRACT_METHODS.transfer).isTransfer).toBe(false);
        });
    });

    describe("isTokenTransfer", () => {
        it("returns true when methodHash matches the transfer contract method", () => {
            expect(method(CONTRACT_METHODS.transfer).isTokenTransfer).toBe(true);
        });

        it("returns false for an unrelated methodHash", () => {
            expect(method(CONTRACT_METHODS.vote).isTokenTransfer).toBe(false);
        });

        it("returns false when methodHash is null", () => {
            expect(method(null).isTokenTransfer).toBe(false);
        });
    });

    describe("isMultiPayment", () => {
        it("returns true when methodHash matches the multipayment contract method", () => {
            expect(method(CONTRACT_METHODS.multipayment).isMultiPayment).toBe(true);
        });

        it("returns false for an unrelated methodHash", () => {
            expect(method(CONTRACT_METHODS.vote).isMultiPayment).toBe(false);
        });
    });

    describe("isVote", () => {
        it("returns true when methodHash matches the vote contract method", () => {
            expect(method(CONTRACT_METHODS.vote).isVote).toBe(true);
        });

        it("returns false for an unrelated methodHash", () => {
            expect(method(CONTRACT_METHODS.transfer).isVote).toBe(false);
        });
    });

    describe("isUnvote", () => {
        it("returns true when methodHash matches the unvote contract method", () => {
            expect(method(CONTRACT_METHODS.unvote).isUnvote).toBe(true);
        });

        it("returns false for an unrelated methodHash", () => {
            expect(method(CONTRACT_METHODS.vote).isUnvote).toBe(false);
        });
    });

    describe("isValidatorRegistration", () => {
        it("returns true when methodHash matches the validator_registration contract method", () => {
            expect(method(CONTRACT_METHODS.validator_registration).isValidatorRegistration).toBe(true);
        });

        it("returns false for an unrelated methodHash", () => {
            expect(method(CONTRACT_METHODS.vote).isValidatorRegistration).toBe(false);
        });
    });

    describe("isValidatorResignation", () => {
        it("returns true when methodHash matches the validator_resignation contract method", () => {
            expect(method(CONTRACT_METHODS.validator_resignation).isValidatorResignation).toBe(true);
        });

        it("returns false for an unrelated methodHash", () => {
            expect(method(CONTRACT_METHODS.vote).isValidatorResignation).toBe(false);
        });
    });

    describe("isValidatorUpdate", () => {
        it("returns true when methodHash matches the validator_update contract method", () => {
            expect(method(CONTRACT_METHODS.validator_update).isValidatorUpdate).toBe(true);
        });

        it("returns false for an unrelated methodHash", () => {
            expect(method(CONTRACT_METHODS.vote).isValidatorUpdate).toBe(false);
        });
    });

    describe("isUsernameRegistration", () => {
        it("returns true when methodHash matches the username_registration contract method", () => {
            expect(method(CONTRACT_METHODS.username_registration).isUsernameRegistration).toBe(true);
        });

        it("returns false for an unrelated methodHash", () => {
            expect(method(CONTRACT_METHODS.vote).isUsernameRegistration).toBe(false);
        });
    });

    describe("isUsernameResignation", () => {
        it("returns true when methodHash matches the username_resignation contract method", () => {
            expect(method(CONTRACT_METHODS.username_resignation).isUsernameResignation).toBe(true);
        });

        it("returns false for an unrelated methodHash", () => {
            expect(method(CONTRACT_METHODS.vote).isUsernameResignation).toBe(false);
        });
    });

    describe("isApprove", () => {
        it("returns true when methodHash matches the approve contract method", () => {
            expect(method(CONTRACT_METHODS.approve).isApprove).toBe(true);
        });

        it("returns false for an unrelated methodHash", () => {
            expect(method(CONTRACT_METHODS.vote).isApprove).toBe(false);
        });
    });

    describe("isRevoke", () => {
        it("returns true when payload matches the revoke method", () => {
            const m = method(CONTRACT_METHODS.approve, {
                payload: {
                    formatted: null,
                    utf8: null,
                    raw: "0x9faf57c0",
                },
                tokenApprovalDetails: {
                    spender: makeMemoryWallet({ address: "spender-address" }),
                    amount: null,
                    isUnlimited: false,
                    isRevoke: true,
                },
            });

            expect(m.isRevoke).toBe(true);
        });

        it("returns false when payload is approve but not revoke", () => {
            const m = method(CONTRACT_METHODS.approve, {
                tokenApprovalDetails: {
                    spender: makeMemoryWallet({ address: "spender-address" }),
                    amount: "100",
                    isUnlimited: false,
                    isRevoke: false,
                },
            });

            expect(m.isRevoke).toBe(false);
        });

        it("returns false when payload is approve and tokenApprovalDetails is null", () => {
            const m = method(CONTRACT_METHODS.approve, { tokenApprovalDetails: null });

            expect(m.isRevoke).toBe(false);
        });

        it("returns false when payload does not match revoke", () => {
            const m = method(CONTRACT_METHODS.vote, {
                tokenApprovalDetails: {
                    spender: makeMemoryWallet({ address: "spender-address" }),
                    amount: null,
                    isUnlimited: false,
                    isRevoke: true,
                },
            });

            expect(m.isRevoke).toBe(false);
        });
    });

    describe("isBatchTransfer", () => {
        it("returns true when methodHash matches the batch_transfer contract method", () => {
            expect(method(CONTRACT_METHODS.batch_transfer).isBatchTransfer).toBe(true);
        });

        it("returns false for an unrelated methodHash", () => {
            expect(method(CONTRACT_METHODS.vote).isBatchTransfer).toBe(false);
        });
    });

    describe("isContractDeployment", () => {
        it("returns true when transaction.to is null", () => {
            expect(method(null, { to: null }).isContractDeployment).toBe(true);
        });

        it("returns false when transaction.to is set", () => {
            expect(method(null, { to: "some-address" }).isContractDeployment).toBe(false);
        });
    });

    describe("isSelfReceiving", () => {
        it("returns true for validator registration", () => {
            expect(method(CONTRACT_METHODS.validator_registration).isSelfReceiving).toBe(true);
        });

        it("returns true for validator resignation", () => {
            expect(method(CONTRACT_METHODS.validator_resignation).isSelfReceiving).toBe(true);
        });

        it("returns true for validator update", () => {
            expect(method(CONTRACT_METHODS.validator_update).isSelfReceiving).toBe(true);
        });

        it("returns true for vote", () => {
            expect(method(CONTRACT_METHODS.vote).isSelfReceiving).toBe(true);
        });

        it("returns true for unvote", () => {
            expect(method(CONTRACT_METHODS.unvote).isSelfReceiving).toBe(true);
        });

        it("returns false for transfer", () => {
            expect(method(null).isSelfReceiving).toBe(false);
        });

        it("returns false for token transfer", () => {
            expect(method(CONTRACT_METHODS.transfer).isSelfReceiving).toBe(false);
        });

        it("returns false for multipayment", () => {
            expect(method(CONTRACT_METHODS.multipayment).isSelfReceiving).toBe(false);
        });
    });

    describe("name", () => {
        it("returns the translated key for a transfer", () => {
            expect(method(null).name({ t: mockT, i18n: { exists: mockI18nExists } })).toBe(
                "general.transaction.types.transfer",
            );
        });

        it("returns the translated key for a token transfer", () => {
            expect(method(CONTRACT_METHODS.transfer).name({ t: mockT, i18n: { exists: mockI18nExists } })).toBe(
                "general.transaction.types.transfer",
            );
        });

        it("returns the translated key for multipayment", () => {
            expect(method(CONTRACT_METHODS.multipayment).name({ t: mockT, i18n: { exists: mockI18nExists } })).toBe(
                "general.transaction.types.multipayment",
            );
        });

        it("returns the translated key for vote", () => {
            expect(method(CONTRACT_METHODS.vote).name({ t: mockT, i18n: { exists: mockI18nExists } })).toBe(
                "general.transaction.types.vote",
            );
        });

        it("returns the translated key for unvote", () => {
            expect(method(CONTRACT_METHODS.unvote).name({ t: mockT, i18n: { exists: mockI18nExists } })).toBe(
                "general.transaction.types.unvote",
            );
        });

        it("returns the translated key for validator registration", () => {
            expect(
                method(CONTRACT_METHODS.validator_registration).name({ t: mockT, i18n: { exists: mockI18nExists } }),
            ).toBe("general.transaction.types.validator-registration");
        });

        it("returns the translated key for validator resignation", () => {
            expect(
                method(CONTRACT_METHODS.validator_resignation).name({ t: mockT, i18n: { exists: mockI18nExists } }),
            ).toBe("general.transaction.types.validator-resignation");
        });

        it("returns the translated key for validator update", () => {
            expect(method(CONTRACT_METHODS.validator_update).name({ t: mockT, i18n: { exists: mockI18nExists } })).toBe(
                "general.transaction.types.validator-update",
            );
        });

        it("returns the translated key for username registration", () => {
            expect(
                method(CONTRACT_METHODS.username_registration).name({ t: mockT, i18n: { exists: mockI18nExists } }),
            ).toBe("general.transaction.types.username-registration");
        });

        it("returns the translated key for username resignation", () => {
            expect(
                method(CONTRACT_METHODS.username_resignation).name({ t: mockT, i18n: { exists: mockI18nExists } }),
            ).toBe("general.transaction.types.username-resignation");
        });

        it("returns the translated key for approve", () => {
            expect(method(CONTRACT_METHODS.approve).name({ t: mockT, i18n: { exists: mockI18nExists } })).toBe(
                "general.transaction.types.approve",
            );
        });

        it("returns the translated key for batch transfer", () => {
            expect(method(CONTRACT_METHODS.batch_transfer).name({ t: mockT, i18n: { exists: mockI18nExists } })).toBe(
                "general.transaction.types.batch-transfer",
            );
        });

        it("returns a formatted name from methodName when no type matches", () => {
            const m = new TransactionMethod(
                makeTransaction({
                    methodData: { functionName: "my_custom_function", methodId: "deadbeef", arguments: [] },
                }),
                network,
            );

            expect(m.name({ t: mockT, i18n: { exists: mockI18nExists } })).toBe("My Custom Function");
        });

        it("strips signature params from methodName and formats as title case", () => {
            const m = new TransactionMethod(
                makeTransaction({
                    methodData: {
                        functionName: "getRounds(uint256,uint256)",
                        methodId: "40f74f47",
                        arguments: [],
                    },
                }),
                network,
            );

            expect(m.name({ t: mockT, i18n: { exists: mockI18nExists } })).toBe("Get Rounds");
        });

        it("falls back to 0x-prefixed hash when no type and no methodName", () => {
            const m = new TransactionMethod(
                makeTransaction({
                    methodData: { functionName: null, methodId: "deadbeef", arguments: [] },
                }),
                network,
            );

            expect(m.name({ t: mockT, i18n: { exists: mockI18nExists } })).toBe("0xdeadbeef");
        });
    });
});
