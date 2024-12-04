@props(['argument'])

{{ ExplorerNumberFormatter::weiToArk(ContractPayload::decodeUnsignedInt($argument)) }}
