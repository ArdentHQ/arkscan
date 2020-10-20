const { Identities, Managers } = require("@arkecosystem/crypto");

Managers.configManager.setFromPreset(process.argv[2]);

console.log(Identities.Address.fromMultiSignatureAsset(JSON.parse(process.argv[3])));
