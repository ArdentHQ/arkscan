# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased Mainnet](https://github.com/ArkEcosystem/explorer/compare/master...staging)

## [Unreleased Devnet](https://github.com/ArkEcosystem/explorer/compare/staging...develop)

## 2022-03-09 (Explorer + Dexplorer)

### Changed

- update PHP dependencies ([#1038]) (c60f4298, @faustbrian)

### Fixed

- legacy multisignature details ([#1041]) (8f1c7873, @alexbarnsley)

---

## 2022-02-14 (Explorer + Dexplorer)

### Fixed

- price graph height on the homepage ([#1033]) (491c9dfb, @leMaur)

### Changed

- remove supervisor for short-schedule ([#1035]) (8f88cf5c, @marianogoldman)
- add dark mode env var ([#1036]) (f4e5b463, @crnkovic)
- use layout head &amp; body components ([#1034]) (31561f45, @alexbarnsley)
- update alpine ([#1016]) (0d159736, @alexbarnsley)
- update PHP dependencies ([#1031]) (9dc5df89, @faustbrian)
- update foundation and remove reddit links ([#1039]) (6d09d22, @itsanametoo)

---

## 2022-01-24 (Explorer + Dexplorer)

### Fixed

- phpstan error &quot;InvalidArgumentException constructor expects string&quot; ([#998]) (66449ce3, @leMaur)
- phpstan error &quot;withScope&quot; ([#997]) (ae032573, @leMaur)
- phpstan error AbstractNetwork ([#996]) (0c64c300, @leMaur)
- js dark mode component initial toggle ([#1021]) (0333cae2, @alexbarnsley)
- switch filter label messing ([#1022]) (b76aa048, @alfonsobries)
- use correct checkmark icon for delegate status ([#1025]) (98692b5, @itsanametoo)
- dynamic truncated text ([#1028]) (92e55c7, @alfonsobries)

### Changed

- setup labeler workflow ([#993]) (38a8dda0, @ItsANameToo)
- phpstan error &quot;Variable method call&quot; ([#999]) (ab92d077, @leMaur)
- phpstan error &quot;PendingDispatch given&quot; ([#995]) (f827c2a5, @leMaur)
- add drop invalid livewire requests middleware ([#1000]) (f1c01e22, @ItsANameToo)
- upgrade to latest version of chartjs ([#981]) (2c01d72a, @Highjhacker)
- use phpstan.neon configuration file from Foundation ([#1005]) (6dde64e4, @leMaur)
- add `host.docker.internal` host to the docker container ([#1013]) (01a992ac, @crnkovic)
- roundness issue on transactions table + chrome ([#1012]) (245fd071, @alfonsobries)
- import coingecko icon ([#1014]) (285ba208, @leMaur)
- update JavaScript dependencies ([#1010]) (8b653d1b, @faustbrian)
- remove `extra_hosts` from docker config ([#1015]) (1b9f86da, @crnkovic)
- use table-compact styling from foundation ([#1011]) (3d8d7360, @alfonsobries)
- add dark theme script ([#1019]) (a6df8bed, @leMaur)
- update PHP dependencies ([#1009]) (4ff7e610, @faustbrian)
- update mobile search design ([#1018]) (fdbdc32a, @alexbarnsley)
- update icon references ([#1017]) (2ff08a74, @leMaur)
- move public key button to match new design ([#1027]) (bf1d494, @alfonsobries)
- only clear views that are older than one hour ([#1026]) (a5e8a2b, @alfonsobries)
- update foundation ([#1029]) (35430a9, @alexbarnsley)
- add composer allow-plugins on docker ([#1030]) (42a41f9, @leMaur)

---

## 2021-11-02 (Explorer + Dexplorer)

### Fixed

- direct linking to delegate table ([#983]) (df5768bf, @alfonsobries)
- user configuration lost ([#987]) (5732b457, @alfonsobries)
- missing icon for confirmed transactions ([#985]) (02e0e832, @leMaur)
- confirmation icon ([#991]) (1cb16f75, @Highjhacker)

### Changed

- use QRCode class from foundation ([#982]) (574b3470, @Highjhacker)
- upgrade foundation dependency ([#986]) (1dca0edb, @alfonsobries)
- handle empty responses from coingecko ([#988]) (71757c72, @alfonsobries)
- show known wallet name when defined ([#990]) (d8751473, @alfonsobries)
- clean up SVGs to prevent clashes ([#989]) (5099b845, @Highjhacker)

---

## 2021-10-18 (Explorer + Dexplorer)

### Added

- implement sentry for error reporting ([#969]) (b166c994, @alfonsobries)
- upgrade deps and add foundation ([#968]) (6081a359, @alfonsobries)

### Fixed

- delegate voters 0 vote weight ([#971]) (b4dcec68, @alexbarnsley)
- onload pagescroll scripts ([#973]) (da65f01d, @leMaur)
- properly display block id in metatags ([#976]) (e36db59c, @Highjhacker)
- delegate table loading state ([#965]) (c5e187d4, @leMaur)
- broken search modal ([#979]) (32ce7bb, @leMaur)

### Changed

- handle new delegate rank and status ([#962]) (6b64bb10, @alexbarnsley)
- display transaction nonce on transaction detail page ([#966]) (2b5397f2, @Highjhacker)
- pollTransactions to be unnecessary called multiple times ([#970]) (c892bdd8, @alfonsobries)
- update page metatitles ([#972]) (67ec2084, @alexbarnsley)
- add view clear command to scheduler ([#977]) (7db434bd, @ItsANameToo)
- cache delegate performance command ([#953]) (02e0b212, @alfonsobries)
- use php-cs-fixer and rector config from foundation ([#974]) (565897e9, @leMaur)

---

## 2021-09-29 (Explorer + Dexplorer)

### Added

- add recipients count next to multipayment transactions ([#940]) (33b8d68f, @alfonsobries)
- add metadata images per section ([#959]) (c677b64a, @alfonsobries)

### Fixed

- add cache-transactions to development data command ([#938]) (99229073, @ItsANameToo)
- `scrollToQuery` incorrect targets ([#944]) (7f7274d0, @alfonsobries)
- ensure it reloads the page when use back button on safari ([#942]) (27a2ad42, @alfonsobries)
- sync navbar price and price blocks in home page ([#935]) (0339fb79, @alfonsobries)
- remove id on all svg icons ([#941]) (a9987791, @alfonsobries)
- pagination not changing page query ([#958]) (420d9a1e, @ItsANameToo)
- delegate table loading state ([#961]) (668f446, @ItsANameToo)
- search page pagination ([#963]) (5489a4b, @leMaur)

### Changed

- removal of unused aggregates and their tests ([#947]) (27f1925d, @Highjhacker)
- change multivote to switch vote ([#943]) (03dc5b4f, @alfonsobries)
- update readme ([#950]) (52eaa4ac, @ItsANameToo)
- optimize delegate voters count command ([#945]) (58b0fe12, @alfonsobries)
- remove plus sign on multipayment ([#955]) (a145ce1d, @alfonsobries)
- update page metadata ([#951]) (fff4e005, @leMaur)
- update dependencies ([#952]) (b5f310b1, @alexbarnsley)
- optimize delegate resignation ids command ([#949]) (f5e9a6e3, @leMaur)
- upgrade oudated npm dependencies ([#954]) (002667ee, @alfonsobries)
- update dependencies ([#956]) (9d727ade, @leMaur)
- update dependencies ([#957]) (e612af3c, @faustbrian)
- update dependencies ([#960]) (d103eeac, @faustbrian)
- use datetime local timezone format ([#930]) (2fd38492, @alexbarnsley)

---

## 2021-09-24 (Explorer + Dexplorer)

### Fixed

- statistics page when switching to non-fiat value ([#931]) (a6f616f3, @leMaur)

### Changed

- optimize cache-transactions command  ([#932]) (ad1d15dd, @alfonsobries)
- optimize cache-fees command ([#933]) (3393d60e, @ItsANameToo)
- reduce update frequency of fees and transaction stats ([#934]) (df2d8cbe, @ItsANameToo)

---

## 2021-09-22 (Explorer + Dexplorer)

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

## 2021-08-23 (Dexplorer)

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

[#733]: https://github.com/ArkEcosystem/explorer/pull/733
[#883]: https://github.com/ArkEcosystem/explorer/pull/883
[#879]: https://github.com/ArkEcosystem/explorer/pull/879
[#887]: https://github.com/ArkEcosystem/explorer/pull/887
[#884]: https://github.com/ArkEcosystem/explorer/pull/884
[#885]: https://github.com/ArkEcosystem/explorer/pull/885
[#888]: https://github.com/ArkEcosystem/explorer/pull/888
[#889]: https://github.com/ArkEcosystem/explorer/pull/889
[#876]: https://github.com/ArkEcosystem/explorer/pull/876
[#890]: https://github.com/ArkEcosystem/explorer/pull/890
[#891]: https://github.com/ArkEcosystem/explorer/pull/891
[#894]: https://github.com/ArkEcosystem/explorer/pull/894
[#892]: https://github.com/ArkEcosystem/explorer/pull/892
[#899]: https://github.com/ArkEcosystem/explorer/pull/899
[#896]: https://github.com/ArkEcosystem/explorer/pull/896
[#895]: https://github.com/ArkEcosystem/explorer/pull/895
[#898]: https://github.com/ArkEcosystem/explorer/pull/898
[#897]: https://github.com/ArkEcosystem/explorer/pull/897
[#901]: https://github.com/ArkEcosystem/explorer/pull/901
[#902]: https://github.com/ArkEcosystem/explorer/pull/902
[#903]: https://github.com/ArkEcosystem/explorer/pull/903
[#905]: https://github.com/ArkEcosystem/explorer/pull/905
[#911]: https://github.com/ArkEcosystem/explorer/pull/911
[#913]: https://github.com/ArkEcosystem/explorer/pull/913
[#906]: https://github.com/ArkEcosystem/explorer/pull/906
[#910]: https://github.com/ArkEcosystem/explorer/pull/910
[#914]: https://github.com/ArkEcosystem/explorer/pull/914
[#915]: https://github.com/ArkEcosystem/explorer/pull/915
[#916]: https://github.com/ArkEcosystem/explorer/pull/916
[#920]: https://github.com/ArkEcosystem/explorer/pull/920
[#921]: https://github.com/ArkEcosystem/explorer/pull/921
[#927]: https://github.com/ArkEcosystem/explorer/pull/927
[#907]: https://github.com/ArkEcosystem/explorer/pull/907
[#908]: https://github.com/ArkEcosystem/explorer/pull/908
[#909]: https://github.com/ArkEcosystem/explorer/pull/909
[#904]: https://github.com/ArkEcosystem/explorer/pull/904
[#919]: https://github.com/ArkEcosystem/explorer/pull/919
[#922]: https://github.com/ArkEcosystem/explorer/pull/922
[#918]: https://github.com/ArkEcosystem/explorer/pull/918
[#923]: https://github.com/ArkEcosystem/explorer/pull/923
[#925]: https://github.com/ArkEcosystem/explorer/pull/925
[#926]: https://github.com/ArkEcosystem/explorer/pull/926
[#924]: https://github.com/ArkEcosystem/explorer/pull/924
[#931]: https://github.com/ArkEcosystem/explorer/pull/931
[#932]: https://github.com/ArkEcosystem/explorer/pull/932
[#933]: https://github.com/ArkEcosystem/explorer/pull/933
[#934]: https://github.com/ArkEcosystem/explorer/pull/934
[#940]: https://github.com/ArkEcosystem/explorer/pull/940
[#959]: https://github.com/ArkEcosystem/explorer/pull/959
[#938]: https://github.com/ArkEcosystem/explorer/pull/938
[#944]: https://github.com/ArkEcosystem/explorer/pull/944
[#942]: https://github.com/ArkEcosystem/explorer/pull/942
[#935]: https://github.com/ArkEcosystem/explorer/pull/935
[#941]: https://github.com/ArkEcosystem/explorer/pull/941
[#958]: https://github.com/ArkEcosystem/explorer/pull/958
[#947]: https://github.com/ArkEcosystem/explorer/pull/947
[#943]: https://github.com/ArkEcosystem/explorer/pull/943
[#950]: https://github.com/ArkEcosystem/explorer/pull/950
[#945]: https://github.com/ArkEcosystem/explorer/pull/945
[#955]: https://github.com/ArkEcosystem/explorer/pull/955
[#951]: https://github.com/ArkEcosystem/explorer/pull/951
[#952]: https://github.com/ArkEcosystem/explorer/pull/952
[#949]: https://github.com/ArkEcosystem/explorer/pull/949
[#954]: https://github.com/ArkEcosystem/explorer/pull/954
[#956]: https://github.com/ArkEcosystem/explorer/pull/956
[#957]: https://github.com/ArkEcosystem/explorer/pull/957
[#960]: https://github.com/ArkEcosystem/explorer/pull/960
[#930]: https://github.com/ArkEcosystem/explorer/pull/930
[#961]: https://github.com/ArkEcosystem/explorer/pull/961
[#963]: https://github.com/ArkEcosystem/explorer/pull/963
[#969]: https://github.com/ArkEcosystem/explorer/pull/969
[#968]: https://github.com/ArkEcosystem/explorer/pull/968
[#971]: https://github.com/ArkEcosystem/explorer/pull/971
[#976]: https://github.com/ArkEcosystem/explorer/pull/976
[#965]: https://github.com/ArkEcosystem/explorer/pull/965
[#937]: https://github.com/ArkEcosystem/explorer/pull/937
[#962]: https://github.com/ArkEcosystem/explorer/pull/962
[#966]: https://github.com/ArkEcosystem/explorer/pull/966
[#970]: https://github.com/ArkEcosystem/explorer/pull/970
[#972]: https://github.com/ArkEcosystem/explorer/pull/972
[#977]: https://github.com/ArkEcosystem/explorer/pull/977
[#953]: https://github.com/ArkEcosystem/explorer/pull/953
[#974]: https://github.com/ArkEcosystem/explorer/pull/974
[#979]: https://github.com/ArkEcosystem/explorer/pull/979
[#983]: https://github.com/ArkEcosystem/explorer/pull/983
[#987]: https://github.com/ArkEcosystem/explorer/pull/987
[#985]: https://github.com/ArkEcosystem/explorer/pull/985
[#991]: https://github.com/ArkEcosystem/explorer/pull/991
[#982]: https://github.com/ArkEcosystem/explorer/pull/982
[#986]: https://github.com/ArkEcosystem/explorer/pull/986
[#988]: https://github.com/ArkEcosystem/explorer/pull/988
[#990]: https://github.com/ArkEcosystem/explorer/pull/990
[#989]: https://github.com/ArkEcosystem/explorer/pull/989
[#998]: https://github.com/ArkEcosystem/explorer/pull/998
[#997]: https://github.com/ArkEcosystem/explorer/pull/997
[#996]: https://github.com/ArkEcosystem/explorer/pull/996
[#1021]: https://github.com/ArkEcosystem/explorer/pull/1021
[#1022]: https://github.com/ArkEcosystem/explorer/pull/1022
[#993]: https://github.com/ArkEcosystem/explorer/pull/993
[#999]: https://github.com/ArkEcosystem/explorer/pull/999
[#995]: https://github.com/ArkEcosystem/explorer/pull/995
[#1000]: https://github.com/ArkEcosystem/explorer/pull/1000
[#981]: https://github.com/ArkEcosystem/explorer/pull/981
[#1005]: https://github.com/ArkEcosystem/explorer/pull/1005
[#1013]: https://github.com/ArkEcosystem/explorer/pull/1013
[#1012]: https://github.com/ArkEcosystem/explorer/pull/1012
[#1014]: https://github.com/ArkEcosystem/explorer/pull/1014
[#1010]: https://github.com/ArkEcosystem/explorer/pull/1010
[#1015]: https://github.com/ArkEcosystem/explorer/pull/1015
[#1011]: https://github.com/ArkEcosystem/explorer/pull/1011
[#1019]: https://github.com/ArkEcosystem/explorer/pull/1019
[#1009]: https://github.com/ArkEcosystem/explorer/pull/1009
[#1018]: https://github.com/ArkEcosystem/explorer/pull/1018
[#1017]: https://github.com/ArkEcosystem/explorer/pull/1017
[#1025]: https://github.com/ArkEcosystem/explorer/pull/1025
[#1026]: https://github.com/ArkEcosystem/explorer/pull/1026
[#1027]: https://github.com/ArkEcosystem/explorer/pull/1027
[#1028]: https://github.com/ArkEcosystem/explorer/pull/1028
[#1029]: https://github.com/ArkEcosystem/explorer/pull/1029
[#1030]: https://github.com/ArkEcosystem/explorer/pull/1030
[#1033]: https://github.com/ArkEcosystem/explorer/pull/1033
[#1035]: https://github.com/ArkEcosystem/explorer/pull/1035
[#1036]: https://github.com/ArkEcosystem/explorer/pull/1036
[#1034]: https://github.com/ArkEcosystem/explorer/pull/1034
[#1016]: https://github.com/ArkEcosystem/explorer/pull/1016
[#1031]: https://github.com/ArkEcosystem/explorer/pull/1031
[#1031]: https://github.com/ArkEcosystem/explorer/pull/1039