# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased Mainnet](https://github.com/ArkEcosystem/explorer.ark.io/compare/master...staging)

## [Unreleased Devnet](https://github.com/ArkEcosystem/explorer.ark.io/compare/staging...develop)

## 2021-24-09 (Explorer + Dexplorer)

### Fixed

- statistics page when switching to non-fiat value ([#931]) (a6f616f3, @leMaur)

### Changed

- optimize cache-transactions command  ([#932]) (ad1d15dd, @alfonsobries)
- optimize cache-fees command ([#933]) (3393d60e, @ItsANameToo)
- reduce update frequency of fees and transaction stats ([#934]) (df2d8cbe, @ItsANameToo)

---

## 2021-22-09 (Explorer + Dexplorer)

### Added

- add wallet uri prefix to the env settings ([#905]) (9ef98940, @alfonsobries)
- replace receive and sent icons ([#911]) (b651408a, @alfonsobries)
- feature: implement coingecko api ([#913]) (e0e01621, @alfonsobries)

### Fixed

- use overflow to prevent gap in headers ([#906]) (7cb73162, @alfonsobries)
- delegate broken headers ([#910]) (92eaa638, @alfonsobries)
- fiat tooltip exchange rate ([#914]) (d5c0850e, @alexbarnsley)
- 24h price change percentage value ([#915]) (980a5dee, @ItsANameToo)
- 24h percentage percent ([#916]) (43e79a73, @ItsANameToo)
- reduce currency fetching delay to avoid timeouts ([#920]) (ac13f388, @ItsANameToo)
- reduce padding between address and balance ([#921]) (752d72a3, @leMaur)
- multipayment sender return status ([#927]) (a9b97c3c, @alexbarnsley)

### Changed

- reduce the margin between voting and header to 1.5 rem ([#907]) (eaf6307c, @alfonsobries)
- hide expanded tables setting on mobile ([#908]) (bfefebf1, @alfonsobries)
- update color of the selected currency ([#909]) (019f32c7, @alfonsobries)
- calculate delegate aggregates in a single query for performance ([#904]) (25472a59, @alfonsobries)
- first column width on delegate tabs ([#919]) (c7660187, @alexbarnsley)
- update arrow direction colour ([#922]) (f0f1c057, @alexbarnsley)
- coingecko footer notice ([#918]) (7f75bc1b, @alexbarnsley)
- increase amount border width ([#923]) (2e61fea1, @alexbarnsley)
- add trusted proxy config ([#925]) (bfd117af, @alexbarnsley)
- update footer css height ([#926]) (c9e23f8e, @alexbarnsley)
- add return transaction status ([#924]) (cbb1948d, @alexbarnsley)

---

## 2021-23-08 (Dexplorer)

### Changed

- implement &quot;chart&quot; component ([#892]) (e0b25d5f, @leMaur)
- persist selected tab on delegate monitor ([#899]) (c6a65a0b, @alfonsobries)
- upgrade laravel framework to version 8.55 ([#901]) (f831a1c3, @Highjhacker)
- wallet info icon color ([#903]) (a8b64fc1, @ItsANameToo)

### Fixed

- status icon disappearing ([#896]) (0ce7c2a5, @alfonsobries)
- disappearing icons on delegate page ([#895]) (06dbf3a9, @alexbarnsley)
- resigned label cropped ([#898]) (ded51820, @alfonsobries)
- truncated block ids inconsistencies ([#897]) (eadf8cb8, @alfonsobries)
- prioritize wallet over blocks in search ([#902]) (abaa59e6, @ItsANameToo)

---

## 2021-08-04 (Dexplorer)

### Added

- statistics (chart) ([#733]) (036338dc, @leMaur)

### Changed

- statistics dropdown wire ignore ([#883]) (02d43e24, @alexbarnsley)
- custom scrollbars in dropdowns ([#879]) (43b60876, @alfonsobries)
- remove configureExplorerDatabase in favour of built-in migrations ([#887]) (e3e0f051, @alfonsobries)
- upgrade arkecosystem/stan ([#884]) (f59aa562, @alfonsobries)

### Fixed

- transaction search taking precedence over block search ([#885]) (f5c9d174, @leMaur)
- broken ID icons ([#888]) (b5dbc74f, @leMaur)
- modal js crash when showing scrollbars ([#889]) (8391b7ee, @ItsANameToo)
- monitor not updating when blocks are missed ([#876]) (f6325cb0, @alexbarnsley)
- transaction icons disappearing when latest transaction table updates ([#890]) (4caea7a4, @leMaur)
- update horizon to support --rest ([#891]) (247d7500, @ItsANameToo)
- use proper address colors for multivote and delegate addresses ([#894]) (f4046c8c, @ItsANameToo)

---

## 2021-07-15 (Dexplorer)

### Changed

- update dark avatar border on table hover (#878) (b74bdcc0, @alexbarnsley)
- include dropdown position js (#880) (df83f84c, @alexbarnsley)

### Fixed

- hide tooltip on small charts (#875) (385cb2ea, @leMaur)
- block gap on safari on statistics page (#877) (3123befc, @leMaur)
- push the generation of a vote report to the queue (#882) (db706909, @crnkovic)

[#733]: https://github.com/ArkEcosystem/explorer.ark.io/pull/733
[#883]: https://github.com/ArkEcosystem/explorer.ark.io/pull/883
[#879]: https://github.com/ArkEcosystem/explorer.ark.io/pull/879
[#887]: https://github.com/ArkEcosystem/explorer.ark.io/pull/887
[#884]: https://github.com/ArkEcosystem/explorer.ark.io/pull/884
[#885]: https://github.com/ArkEcosystem/explorer.ark.io/pull/885
[#888]: https://github.com/ArkEcosystem/explorer.ark.io/pull/888
[#889]: https://github.com/ArkEcosystem/explorer.ark.io/pull/889
[#876]: https://github.com/ArkEcosystem/explorer.ark.io/pull/876
[#890]: https://github.com/ArkEcosystem/explorer.ark.io/pull/890
[#891]: https://github.com/ArkEcosystem/explorer.ark.io/pull/891
[#894]: https://github.com/ArkEcosystem/explorer.ark.io/pull/894
[#892]: https://github.com/ArkEcosystem/explorer.ark.io/pull/892
[#899]: https://github.com/ArkEcosystem/explorer.ark.io/pull/899
[#896]: https://github.com/ArkEcosystem/explorer.ark.io/pull/896
[#895]: https://github.com/ArkEcosystem/explorer.ark.io/pull/895
[#898]: https://github.com/ArkEcosystem/explorer.ark.io/pull/898
[#897]: https://github.com/ArkEcosystem/explorer.ark.io/pull/897
[#901]: https://github.com/ArkEcosystem/explorer.ark.io/pull/901
[#902]: https://github.com/ArkEcosystem/explorer.ark.io/pull/902
[#903]: https://github.com/ArkEcosystem/explorer.ark.io/pull/903
[#905]: https://github.com/ArkEcosystem/explorer.ark.io/pull/905
[#911]: https://github.com/ArkEcosystem/explorer.ark.io/pull/911
[#913]: https://github.com/ArkEcosystem/explorer.ark.io/pull/913
[#906]: https://github.com/ArkEcosystem/explorer.ark.io/pull/906
[#910]: https://github.com/ArkEcosystem/explorer.ark.io/pull/910
[#914]: https://github.com/ArkEcosystem/explorer.ark.io/pull/914
[#915]: https://github.com/ArkEcosystem/explorer.ark.io/pull/915
[#916]: https://github.com/ArkEcosystem/explorer.ark.io/pull/916
[#920]: https://github.com/ArkEcosystem/explorer.ark.io/pull/920
[#921]: https://github.com/ArkEcosystem/explorer.ark.io/pull/921
[#927]: https://github.com/ArkEcosystem/explorer.ark.io/pull/927
[#907]: https://github.com/ArkEcosystem/explorer.ark.io/pull/907
[#908]: https://github.com/ArkEcosystem/explorer.ark.io/pull/908
[#909]: https://github.com/ArkEcosystem/explorer.ark.io/pull/909
[#904]: https://github.com/ArkEcosystem/explorer.ark.io/pull/904
[#919]: https://github.com/ArkEcosystem/explorer.ark.io/pull/919
[#922]: https://github.com/ArkEcosystem/explorer.ark.io/pull/922
[#918]: https://github.com/ArkEcosystem/explorer.ark.io/pull/918
[#923]: https://github.com/ArkEcosystem/explorer.ark.io/pull/923
[#925]: https://github.com/ArkEcosystem/explorer.ark.io/pull/925
[#926]: https://github.com/ArkEcosystem/explorer.ark.io/pull/926
[#924]: https://github.com/ArkEcosystem/explorer.ark.io/pull/924
[#931]: https://github.com/ArkEcosystem/explorer.ark.io/pull/931
[#932]: https://github.com/ArkEcosystem/explorer.ark.io/pull/932
[#933]: https://github.com/ArkEcosystem/explorer.ark.io/pull/933
[#934]: https://github.com/ArkEcosystem/explorer.ark.io/pull/934
