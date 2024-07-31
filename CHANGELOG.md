## [0.15.1](https://github.com/miaoxing/plugin/compare/v0.15.0...v0.15.1) (2024-07-31)





### Dependencies

* **@miaoxing/dev:** upgrade from `9.1.1` to `9.1.2`
* **@mxjs/cli:** upgrade from `0.1.16` to `0.1.17`
* **@wei/wei:** upgrade from `0.17.5` to `0.17.6`

# [0.15.0](https://github.com/miaoxing/plugin/compare/v0.14.7...v0.15.0) (2024-06-30)


### Bug Fixes

* **plugin:** 增加 `symfony/console` 依赖 ([6845ca0](https://github.com/miaoxing/plugin/commit/6845ca0fa737a401c4a6663a20e49be2a0bf5ef4))


### Code Refactoring

* **plugin:** `App` 移除自动注入参数到控制器的功能 ([873b202](https://github.com/miaoxing/plugin/commit/873b202784be54ea8f614dd08d797ee84183029d))


### BREAKING CHANGES

* **plugin:** `App` 移除自动注入参数到控制器的功能

## [0.14.7](https://github.com/miaoxing/plugin/compare/v0.14.6...v0.14.7) (2024-05-30)


### Features

* **plugin:** `g:plugin` 标题为空时根据插件 id 生成标题，id 传入 snake 格式时转换为 dash 格式 ([aad6e5d](https://github.com/miaoxing/plugin/commit/aad6e5d34f233ae74cd54e645a2a7a2771b733b9))
* **plugin:** 增加 `g:plugin` 插件 ([7570851](https://github.com/miaoxing/plugin/commit/7570851e9a5f437496cff12e0940557066f15a91))





### Dependencies

* **@wei/wei:** upgrade from `0.17.4` to `0.17.5`

## [0.14.6](https://github.com/miaoxing/plugin/compare/v0.14.5...v0.14.6) (2024-05-01)


### Bug Fixes

* **config:** 类型为 null 时获取名称错误 ([b19f466](https://github.com/miaoxing/plugin/commit/b19f466ba898db59f4bf88161bb2d243536bcc03))

## [0.14.5](https://github.com/miaoxing/plugin/compare/v0.14.4...v0.14.5) (2024-03-31)


### Bug Fixes

* **plugin): class_exists(:** Passing null to parameter [#1](https://github.com/miaoxing/miaoxing/issues/1) ($class) of type string is deprecated ([9885840](https://github.com/miaoxing/plugin/commit/9885840d87638d15eea98a29aba6537c6c3a5738))


### Features

* **plugin:** `RedisQueue` 改为通过 stream 实现 ([bde1002](https://github.com/miaoxing/plugin/commit/bde10024b8d87f91b67eef00bf770634f77450be))
* **plugin:** 增加 `g:job` 命令，用于生成队列任务 ([fdfd425](https://github.com/miaoxing/plugin/commit/fdfd425f97c92835de2dca0be8265f3d19c28108))
* **plugin:** 增加 `queue:listen` 命令，用于解决更改代码需重启 worker 的问题 ([f578a56](https://github.com/miaoxing/plugin/commit/f578a569bb8bcc3d5b6bb40e37d6c5495abffa35))
* **plugin:** 增加 `Queue` 相关服务 ([69ea101](https://github.com/miaoxing/plugin/commit/69ea101859e23d8cf7a325c0944a25efb96d8f99))
* **plugin:** 增加队列相关命令 ([0e5bb7d](https://github.com/miaoxing/plugin/commit/0e5bb7dbe9dce2a9a72a4cb409d8b2b0f84405ce))
* **plugin:** 重写队列 ([1f6cdf0](https://github.com/miaoxing/plugin/commit/1f6cdf05eefbc46a9ef3f403f076d815291b7019))
* **plugin:** 重写队列 2 ([85db3a7](https://github.com/miaoxing/plugin/commit/85db3a72680d81ef1dbb1f218282daa574657019))
* **plugin:** 重写队列 3 ([7e469e2](https://github.com/miaoxing/plugin/commit/7e469e26368ef52fbaaf6a65e79b261520271eb6))
* **plugin:** 重写队列 4 ([8df4fc6](https://github.com/miaoxing/plugin/commit/8df4fc676fc2c2832ac02375906beb5c02526b7f))
* **plugin, experimental:** 增加 `ConsoleApp` 服务，用于命令行脚本 ([cafa768](https://github.com/miaoxing/plugin/commit/cafa768e390ca0be19ea68aa51ecec6bce0e6932))





### Dependencies

* **@miaoxing/dev:** upgrade from `9.1.0` to `9.1.1`
* **@mxjs/cli:** upgrade from `0.1.15` to `0.1.16`
* **@wei/wei:** upgrade from `0.17.3` to `0.17.4`

## [0.14.4](https://github.com/miaoxing/plugin/compare/v0.14.3...v0.14.4) (2024-02-29)


### Bug Fixes

* **plugin:** `Jwt` token 未到时间就过期 ([cd2d27d](https://github.com/miaoxing/plugin/commit/cd2d27d4550f9e1455876c9ecc84fea4af9412b9))
* **plugin, php8:** Creation of dynamic property xxx is deprecated ([0b6a0a3](https://github.com/miaoxing/plugin/commit/0b6a0a3dc3c798670bdcfe5fa4f47a5c54e0fbfc))


### Features

* **plugin, experimental:** 增加 `Schedule` 服务和相关功能 ([7b6f13a](https://github.com/miaoxing/plugin/commit/7b6f13aadb839767556c5e13ce084a66ec3727bd))

## [0.14.3](https://github.com/miaoxing/plugin/compare/v0.14.2...v0.14.3) (2024-02-20)





### Dependencies

* **@wei/wei:** upgrade from `0.17.2` to `0.17.3`

## [0.14.2](https://github.com/miaoxing/plugin/compare/v0.14.1...v0.14.2) (2024-01-08)





### Dependencies

* **@miaoxing/dev:** upgrade from `9.0.0` to `9.1.0`
* **@mxjs/cli:** upgrade from `0.1.14` to `0.1.15`
* **@wei/wei:** upgrade from `0.17.1` to `0.17.2`

## [0.14.1](https://github.com/miaoxing/plugin/compare/v0.14.0...v0.14.1) (2023-12-31)





### Dependencies

* **@miaoxing/dev:** upgrade from `8.2.4` to `9.0.0`
* **@mxjs/cli:** upgrade from `0.1.13` to `0.1.14`
* **@wei/wei:** upgrade from `0.17.0` to `0.17.1`

# [0.14.0](https://github.com/miaoxing/plugin/compare/v0.13.2...v0.14.0) (2023-11-30)


### Bug Fixes

* **Jwt:** PHP 8 remove deprecated openssl_free_key function ([69e53f4](https://github.com/miaoxing/plugin/commit/69e53f4404a8246005a4e1e5c8370e613d22d78b))
* **plugin:** phpstan EventList Parameter [#1](https://github.com/miaoxing/miaoxing/issues/1) $id of method Miaoxing\Plugin\Service\App::setId() expects string|null, int given. ([6b5c10f](https://github.com/miaoxing/plugin/commit/6b5c10f3e20ac3a5f753b39525555a3ce37e40f4))
* **plugin:** 移除 phpstan 版本限制 ([583de45](https://github.com/miaoxing/plugin/commit/583de4508b195bfabecba42837654b3ff8a17c85))
* **Plugin:** PHP 8.1 Passing null to parameter of type string is deprecated ([642be41](https://github.com/miaoxing/plugin/commit/642be41e085e178762894c6c25221a06324484d6))


### Features

* **Jwt:** 底层实现更换为 `adhocore/jwt`，以便兼容 PHP 7 和 8，更新 Jwt 服务的方法和返回值 ([b1461d2](https://github.com/miaoxing/plugin/commit/b1461d2c6d6eefb46405667973f7322a81a1e456))
* **plugin:** `GAutoCompletion` 生成 `wei` 函数增加返回值和参数 ([a433fb0](https://github.com/miaoxing/plugin/commit/a433fb00dc729f35d64f8516e17a49cc7bb39e43))
* **plugin:** `GAutoCompletion` 生成的类文件增加 `AllowDynamicProperties` ([3ae1deb](https://github.com/miaoxing/plugin/commit/3ae1deb8cd17577fa12897e860780768abadcfd8))


### BREAKING CHANGES

* **Jwt:** 底层实现更换为 `adhocore/jwt`，以便兼容 PHP 7 和 8，更新 Jwt 服务的方法和返回值





### Dependencies

* **@miaoxing/dev:** upgrade from `8.2.3` to `8.2.4`
* **@mxjs/cli:** upgrade from `0.1.12` to `0.1.13`
* **@wei/wei:** upgrade from `0.16.0` to `0.17.0`

## [0.13.2](https://github.com/miaoxing/plugin/compare/v0.13.1...v0.13.2) (2023-11-02)


### Features

* **plugin:** `ReqQuery` 支持设置 `reqRelations` 来允许查询关联数据 ([9825d8d](https://github.com/miaoxing/plugin/commit/9825d8dca71c7ff9ee4c33391937b5a61d24af43))
* **plugin:** 增加 `config:preload` 命令，用于更新过期的预加载配置 ([7c2a521](https://github.com/miaoxing/plugin/commit/7c2a521894a986cb439c2f3f4fe15f196a3ede36))
* **plugin, deprecated:** `ReqQuery` 废弃通过 `isRelation` 判断关联来查询数据 ([2b960f4](https://github.com/miaoxing/plugin/commit/2b960f44b80930b6a6831778b31565cc5d7f0c29))
* **plugin, experimental:** 预加载配置时，默认不检查是否要更新配置 ([6595431](https://github.com/miaoxing/plugin/commit/6595431c63a8d3e5d4243b22f615f89495b6cec2))





### Dependencies

* **@wei/wei:** upgrade from `0.15.12` to `0.16.0`

## [0.13.1](https://github.com/miaoxing/plugin/compare/v0.13.0...v0.13.1) (2023-09-30)


### Bug Fixes

* **plugin:** `Tester` 捕获异常应为基类 ([29dd304](https://github.com/miaoxing/plugin/commit/29dd30457039b86671f817ac3f7f10916166d63d))


### Features

* **plugin:** `plugin:use` 命令允许留空参数来输出当前使用的插件 ([c43e063](https://github.com/miaoxing/plugin/commit/c43e063a31a6ca59a461f43cf31b7453c09d731b))
* **plugin:** 废弃 `HandleRetTrait`，改为使用 `$ret->assert` 方法 ([3133160](https://github.com/miaoxing/plugin/commit/3133160e674e084d6fd595d28c87ed9a38315268))
* **plugin, deprecated:** `Plugin` 废弃 `ignoredServices` 属性，改为使用 `[@ignored](https://github.com/ignored)` 注释 ([d877f08](https://github.com/miaoxing/plugin/commit/d877f08e98016711358ec9e1e77415be2b53cc92))
* **plugin, deprecated:** 废弃 `Miaoxing\Plugin\RetException` ([f515cee](https://github.com/miaoxing/plugin/commit/f515cee711b340d82bde774224b162fae6227a36))





### Dependencies

* **@wei/wei:** upgrade from `0.15.11` to `0.15.12`

# [0.13.0](https://github.com/miaoxing/plugin/compare/v0.12.2...v0.13.0) (2023-09-02)


### Bug Fixes

* **plugin:** `/api/admin/` 接口地址未判断为后台 ([c22281b](https://github.com/miaoxing/plugin/commit/c22281b9f5803e1ebd8c949d920f028a8d9236f4))


### Code Refactoring

* **App:** 移除旧版的 `isRet` 和 `handleRet` 方法 ([9909296](https://github.com/miaoxing/plugin/commit/9909296a65acf60327f23b0a023c8bf10d4c925f))


### Features

* **deprecated:** 废弃 `Miaoxing\Services\Service\Http`，改用 `Wei\Http` ([7184758](https://github.com/miaoxing/plugin/commit/7184758b020f10542eeb89e974558ed4aa9f6923))
* **GMigration:** 支持读取默认插件 ([6e5aca9](https://github.com/miaoxing/plugin/commit/6e5aca93cdb6e4c714239e1d48cc613acf049557))
* **plugin:** `App` 允许调用 `getAction` 获取当前执行的操作名称 ([c8642b8](https://github.com/miaoxing/plugin/commit/c8642b8452d1bd834b23e2d3c6fa95efcf4308ba))
* **plugin:** `App` 初步实现 `dispatch` 方法，允许传入参数调用指定的操作 ([21725e0](https://github.com/miaoxing/plugin/commit/21725e0edff2a3c63f3f57481e030715fee45f06))


### BREAKING CHANGES

* **App:** `App` 移除旧版的 `isRet` 和 `handleRet` 方法





### Dependencies

* **@miaoxing/dev:** upgrade from `8.2.2` to `8.2.3`
* **@mxjs/cli:** upgrade from `0.1.11` to `0.1.12`
* **@wei/wei:** upgrade from `0.15.10` to `0.15.11`

## [0.12.2](https://github.com/miaoxing/plugin/compare/v0.12.1...v0.12.2) (2023-07-31)


### Features

* **plugin:** `Config/GlobalConfigModel` 增加 `typeName` 虚拟属性，引入 `ReqQueryTrait` 等 ([36a72a4](https://github.com/miaoxing/plugin/commit/36a72a4c4387cf149ca8897188baa02f54e07917))
* **plugin:** 安装插件时，同时安装依赖的插件 ([67521e8](https://github.com/miaoxing/plugin/commit/67521e829832b00d0bcd5afadd0f4b2b0708e637))
* **plugin, experimental:** `Config` 增加 `update/deleteCache` 方法，用于更新/删除配置缓存 ([cab4fb7](https://github.com/miaoxing/plugin/commit/cab4fb700cfd2a8099417ace420a9ab72d0bef0d))





### Dependencies

* **@miaoxing/dev:** upgrade from `8.2.1` to `8.2.2`
* **@mxjs/cli:** upgrade from `0.1.10` to `0.1.11`
* **@wei/wei:** upgrade from `0.15.9` to `0.15.10`

## [0.12.1](https://github.com/miaoxing/plugin/compare/v0.12.0...v0.12.1) (2023-06-30)


### Features

* **app:** 接口兼容使用 `api` 路径开头，移动端接口允许不以 m 开头 ([33b7c8b](https://github.com/miaoxing/plugin/commit/33b7c8b64b2cfd7aec3f1f973958db003f6941f1))
* **plugin, experimental:** `App` 增加 `getCurControllerInstance` 方法，用于获取当前页面对象 ([bc3f69d](https://github.com/miaoxing/plugin/commit/bc3f69d63b11161230722c495aed83799075fd9e))





### Dependencies

* **@wei/wei:** upgrade from `0.15.8` to `0.15.9`

# [0.12.0](https://github.com/miaoxing/plugin/compare/v0.11.2...v0.12.0) (2023-05-31)


### Code Refactoring

* **plugin:** `g:auto-completion` 命令不再生成视图变量 ([21661b6](https://github.com/miaoxing/plugin/commit/21661b60304da4df1bc1982b28a552c611ecc66f))


### Features

* **antd5:** 更新 `antd` 到 `5.1.6` ([00f3740](https://github.com/miaoxing/plugin/commit/00f3740fc7717f0cb0ad08250593931c5557bdf2))
* **plugin:** HTTP 请求都开启 header，以便记录日志 ([12b14c1](https://github.com/miaoxing/plugin/commit/12b14c10b9d927aeef2054aa648416b3dc6ef064))


### BREAKING CHANGES

* **plugin:** `g:auto-completion` 命令不再生成视图变量





### Dependencies

* **@miaoxing/dev:** upgrade from `8.2.0` to `8.2.1`
* **@mxjs/cli:** upgrade from `0.1.9` to `0.1.10`
* **@wei/wei:** upgrade from `0.15.7` to `0.15.8`

## [0.11.2](https://github.com/miaoxing/plugin/compare/v0.11.1...v0.11.2) (2023-04-30)


### Features

* **plugin:** 页面不存在时，默认展示 404 ([26a8ed6](https://github.com/miaoxing/plugin/commit/26a8ed60224966eafe41ad01131bf9911d7ee926))





### Dependencies

* **@wei/wei:** upgrade from `0.15.6` to `0.15.7`

## [0.11.1](https://github.com/miaoxing/plugin/compare/v0.11.0...v0.11.1) (2023-04-15)


### Bug Fixes

* **plugin:** `PageRouter` 生成页面支持合并多级目录 ([1aa451e](https://github.com/miaoxing/plugin/commit/1aa451ebd72314b8e4cc3782b49b26a761375ca7))


### Features

* **plugin:** 增加 `httpComplete` 事件，在 HTTP 请求完成时触发 ([269ff1d](https://github.com/miaoxing/plugin/commit/269ff1da69b5501150b3ba21287e112bd2d9d707))
* **plugin:** 增加超级管理员逻辑 ([68c9469](https://github.com/miaoxing/plugin/commit/68c9469128f2d45d5ce07eef9de822c26791900f))





### Dependencies

* **@wei/wei:** upgrade from `0.15.5` to `0.15.6`

# [0.11.0](https://github.com/miaoxing/plugin/compare/v0.10.1...v0.11.0) (2023-03-01)


### Bug Fixes

* **plugin:** `App` 执行方法前，将请求方式转换为小写，以便和方法名称一致 ([f838e41](https://github.com/miaoxing/plugin/commit/f838e41a8564fe7906e300a831fefd7573de7599))


### Features

* **plugin:** `user` 服务改为调用 `userModel` 的方法, 不继承 `userModel` ([30ee2a6](https://github.com/miaoxing/plugin/commit/30ee2a6245c621da14fbac807584c04a0ba5a2fd))
* **plugin:** 废弃 `Miaoxing\Plugin\Service\IsModelExists` 改用 `Wei\IsModelExists` ([db58233](https://github.com/miaoxing/plugin/commit/db582333f62c5c066e6a757d6960e4f3aa261716))
* **plugin, GAutoCompletion:** 增加生成 `XxxPropMixin`，只包含服务属性，不含服务方法 ([4f5f36e](https://github.com/miaoxing/plugin/commit/4f5f36e5b6f3e709d9829946eea5355796734b8c))


### BREAKING CHANGES

* **plugin:** `user` 服务改为调用 `userModel` 的方法, 不继承 `userModel`





### Dependencies

* **@wei/wei:** upgrade from `0.15.4` to `0.15.5`

## [0.10.1](https://github.com/miaoxing/plugin/compare/v0.10.0...v0.10.1) (2023-01-31)


### Features

* **Model:** 增加 `IpTrait`，用于为模型记录用户 IP 和端口 ([0b3f5a1](https://github.com/miaoxing/plugin/commit/0b3f5a10d81653342f8d79e20e5d28a3bfb2d73f))
* **plugin:** 实现生成模型关联 ([3ebc66e](https://github.com/miaoxing/plugin/commit/3ebc66e4b694dcd8d9be225110f0c202701da53b))
* **plugin:** 废弃 `Miaoxing\Plugin\Service\Snowflake`，改用 `Wei\Snowflake` ([e608200](https://github.com/miaoxing/plugin/commit/e60820024c3acde1d9fa3d0b630f25d64be4395f))





### Dependencies

* **@wei/wei:** upgrade from `0.15.3` to `0.15.4`

# [0.10.0](https://github.com/miaoxing/plugin/compare/v0.9.4...v0.10.0) (2023-01-01)


### Code Refactoring

* **plugin:** `App` 移除废弃的 `getControllerFile` 和 `isApi` 方法 ([fa56d0f](https://github.com/miaoxing/plugin/commit/fa56d0f825a1b6a66174209435f45a90981fa665))
* **plugin:** `BaseController` 移除废弃的功能，包括 `getControllerName`，`getActionName` 等 ([a6fd76b](https://github.com/miaoxing/plugin/commit/a6fd76b4251a50e91c1dd029d703f44acf4a3675))
* **plugin:** `BaseTestCase` 移除废弃的 `step` 方法 ([7f9e227](https://github.com/miaoxing/plugin/commit/7f9e227f98aeb456840d92a0a75ecaf43dbc9735))
* **plugin:** `ModelTrait` 移除废弃的 `__invoke` 方法，改用 `$wei->get($modelName)` 返回模型实例 ([4c5f5df](https://github.com/miaoxing/plugin/commit/4c5f5df8c85c968093798a5fe45e181e83009830))


### Features

* **plugin:** `BasePage` 增加 `pageInit` 事件 ([65a2847](https://github.com/miaoxing/plugin/commit/65a284705dd42fef2c22a665eba8a1a98b323e07))
* **plugin:** GMetadata 支持识别 `binary` 和 `varbinary` 为 `string` ([1bfe2b4](https://github.com/miaoxing/plugin/commit/1bfe2b41e2bdb22666029ae96e46113ab86cea43))


### BREAKING CHANGES

* **plugin:** `ModelTrait` 移除废弃的 `__invoke` 方法，改用 `$wei->get($modelName)` 返回模型实例
* **plugin:** `App` 移除废弃的 `getControllerFile` 和 `isApi` 方法
* **plugin:** `BaseController` 移除废弃的功能，包括 `getControllerName`，`getActionName` 等
* **plugin:** `BaseTestCase` 移除废弃的 `step` 方法





### Dependencies

* **@miaoxing/dev:** upgrade from `8.1.3` to `8.2.0`
* **@mxjs/cli:** upgrade from `0.1.8` to `0.1.9`
* **@wei/wei:** upgrade from `0.15.2` to `0.15.3`

## [0.9.4](https://github.com/miaoxing/plugin/compare/v0.9.3...v0.9.4) (2022-12-01)


### Features

* **isBigIntString:** 增加 `isBigIntString` 校验器 ([2913102](https://github.com/miaoxing/plugin/commit/291310270f08368541048c1c9c48d5cd5d5f97da))
* **Upload:** 增加音频扩展名和相关操作 ([5ab9084](https://github.com/miaoxing/plugin/commit/5ab90847f5dfc5155a5af2e4c0b245d91681b4ec))





### Dependencies

* **@wei/wei:** upgrade from `0.15.1` to `0.15.2`

## [0.9.3](https://github.com/miaoxing/plugin/compare/v0.9.2...v0.9.3) (2022-11-01)


### Bug Fixes

* 校验增加长度检查 ([4d22835](https://github.com/miaoxing/plugin/commit/4d2283511d5c0c795524c056d322cd2eebdb8395))


### Features

* **plugin:** `g:metadata` 支持使用默认插件编号 ([11b0246](https://github.com/miaoxing/plugin/commit/11b02466f8248fc0edddfd744d53e075b8a1bc3e))
* **plugin:** 增加 `PluginUse` 和 `PluginUnuse` 命令，用于记录和删除默认使用的插件编号 ([3db9834](https://github.com/miaoxing/plugin/commit/3db98344cf72625d6693595f3da73089690e7ebb))
* **plugin:** 带插件参数的命令都支持使用默认插件编号 ([2b93e51](https://github.com/miaoxing/plugin/commit/2b93e515445208ec32f36147722064d1a65b5ead))





### Dependencies

* **@wei/wei:** upgrade from `0.15.0` to `0.15.1`

## [0.9.2](https://github.com/miaoxing/plugin/compare/v0.9.1...v0.9.2) (2022-09-30)


### Features

* **plugin:** 增加 `cache:get` 命令 ([66b80c0](https://github.com/miaoxing/plugin/commit/66b80c0554b7a6770f349a01075d3d4d405e4ea6))





### Dependencies

* **@wei/wei:** upgrade from `0.14.0` to `0.15.0`

## [0.9.1](https://github.com/miaoxing/plugin/compare/v0.9.0...v0.9.1) (2022-09-03)


### Features

* **plugin:** `cache:clear` 命令允许指定缓存名称 ([1ebcbd6](https://github.com/miaoxing/plugin/commit/1ebcbd65bbbd89a3d9c705e755738b168976a9d6))
* 增加 `cache:delete` 命令，可用于删除指定名称的缓存 ([ce81f06](https://github.com/miaoxing/plugin/commit/ce81f0616395a134f52bcb62fe398fd2a0a7cc1b))
* **plugin:** `g:migration` 增加 `migration:g` 别名 ([19369e7](https://github.com/miaoxing/plugin/commit/19369e729e78c8f412fe64792d728d3dae244ed7))
* **plugin:** 增加 `beforeUserLogout` 事件 ([81772a3](https://github.com/miaoxing/plugin/commit/81772a344ae77373ba5cfe0e6b18d45735ca98ee))
* **plugin:** 捕获 Ret 异常支持 `Wei\Ret\RetException` 基类 ([01d21dd](https://github.com/miaoxing/plugin/commit/01d21dd30a4d6b92076bba17bd279940f77abd69))





### Dependencies

* **@wei/wei:** upgrade from `0.13.0` to `0.14.0`

# [0.9.0](https://github.com/miaoxing/plugin/compare/v0.8.5...v0.9.0) (2022-08-02)


### Code Refactoring

* **Cls:** 移动 `Cls` 到 `wei` 中 ([27d469b](https://github.com/miaoxing/plugin/commit/27d469b3f9307f5661b4b822782bbfdad622a998))
* **Cls:** 移动 `Str` 到 `wei` 中 ([809f80f](https://github.com/miaoxing/plugin/commit/809f80f434d46ea9e37ba538ead9e2adef85ecfc))
* **Model:** 移动模型基类到 `wei` 中 ([a9de076](https://github.com/miaoxing/plugin/commit/a9de07675fc2d87090d55ad7bd6d119ef1b00601))
* **QueryBuilder:** 移动 `QueryBuilder` 到 `wei` 中 ([2f9a1f2](https://github.com/miaoxing/plugin/commit/2f9a1f212726ff4981cd11549dc6417966375968))


### Features

* **GAutoCompletion:** 生成代码提示支持新的校验器写法 ([85ff9c5](https://github.com/miaoxing/plugin/commit/85ff9c5bcd216bdbccf2aea0af587abfb0101680))


### BREAKING CHANGES

* **Model:** 移动模型基类到 `wei` 中
* **Cls:** 移动 `Str` 到 `wei` 中
* **Cls:** 移动 `Cls` 到 `wei` 中
* **QueryBuilder:** 移动 `QueryBuilder` 到 `wei` 中





### Dependencies

* **@miaoxing/dev:** upgrade from `8.1.2` to `8.1.3`
* **@mxjs/cli:** upgrade from `0.1.7` to `0.1.8`
* **@wei/wei:** upgrade from `0.12.6` to `0.13.0`

## [0.8.5](https://github.com/miaoxing/plugin/compare/v0.8.4...v0.8.5) (2022-07-02)


### Bug Fixes

* 解决 composer 2.2+ 默认不启用插件导致安装路径错误 ([c1cdbda](https://github.com/miaoxing/plugin/commit/c1cdbdaaca1cf5b1002291da7ba4f452fe1e0d25))





### Dependencies

* **@wei/wei:** upgrade from `0.12.5` to `0.12.6`

## [0.8.4](https://github.com/miaoxing/plugin/compare/v0.8.3...v0.8.4) (2022-07-01)


### Features

* **plugin:** `SoftDeleteTrait` 增加回收站功能 ([185566a](https://github.com/miaoxing/plugin/commit/185566a3137f94f25846499156b0a1d459202cf5))
* **plugin:** 增加 `BasePage` 类，作为页面的基类 ([b86625a](https://github.com/miaoxing/plugin/commit/b86625a893cf9865e8f00b9fd39e1f14f17498e8))
* **plugin:** 增加 `cache:clear` 命令 ([9d67360](https://github.com/miaoxing/plugin/commit/9d6736030816261e2463de672d974835ca22baa9))





### Dependencies

* **@miaoxing/dev:** upgrade from `8.1.1` to `8.1.2`
* **@mxjs/cli:** upgrade from `0.1.6` to `0.1.7`
* **@wei/wei:** upgrade from `0.12.4` to `0.12.5`

## [0.8.3](https://github.com/miaoxing/plugin/compare/v0.8.2...v0.8.3) (2022-06-01)


### Features

* 演示模式下，提交修改密码后不会改变原密码 ([f220dcd](https://github.com/miaoxing/plugin/commit/f220dcd76420ce1b9691082fd9c5ea7fad88a0b0))
* **plugin, App:** 增加 `App::isDemo` 来判断应用是否处于演示模式 ([d09f7b2](https://github.com/miaoxing/plugin/commit/d09f7b2780391c3b2b10d29c90cf1e32b98101bf))
* **plugin, isUBigIntString, experimental:** 增加 `IsUBigIntString` 校验服务，用于校验 `uBigInt`，同时允许空字符串 ([df9b764](https://github.com/miaoxing/plugin/commit/df9b764af566a3e15fd1106045c67607fc81015a))





### Dependencies

* **@miaoxing/dev:** upgrade from `8.1.0` to `8.1.1`
* **@mxjs/cli:** upgrade from `0.1.5` to `0.1.6`
* **@wei/wei:** upgrade from `0.12.3` to `0.12.4`

## [0.8.2](https://github.com/miaoxing/plugin/compare/v0.8.1...v0.8.2) (2022-04-30)


### Features

* **plugin, internal:** 启用应用时就设置好标头信息 ([ff1baf2](https://github.com/miaoxing/plugin/commit/ff1baf2f23ac399c6a0c8b5964f8b2e7de8673e1))
* **plugin, Model:** 增加 `decrSave` 方法 ([b22a8b3](https://github.com/miaoxing/plugin/commit/b22a8b309a437d2a568dbe2858a029edc6ce507b))





### Dependencies

* **@wei/wei:** upgrade from `0.12.2` to `0.12.3`

## [0.8.1](https://github.com/miaoxing/plugin/compare/v0.8.0...v0.8.1) (2022-03-31)


### Bug Fixes

* **App:** `setId` 传入参数应为字符串 ([b8ffe69](https://github.com/miaoxing/plugin/commit/b8ffe6936375456ec56e1da94476d603bd82a675))
* **plugin:** 保存当前用户再读取，缓存未更新 ([40bf9bb](https://github.com/miaoxing/plugin/commit/40bf9bbaf76e5908467b434c9d63fbeb3ca313ec))
* 传入 app 参数应设置为 app id ([f3cc29f](https://github.com/miaoxing/plugin/commit/f3cc29fcb9ebe37ce87aba1de4e9e69738d3d614))





### Dependencies

* **@wei/wei:** upgrade from `0.12.1` to `0.12.2`

# [0.8.0](https://github.com/miaoxing/plugin/compare/v0.7.1...v0.8.0) (2022-03-04)


### Code Refactoring

* **app:** 应用编号改为字符串，默认读取第一个应用 ([cbcb80f](https://github.com/miaoxing/plugin/commit/cbcb80fc7e9353e429b98a6de08c14435294bb24))


### Features

* 数据表 `app_id` 字段由 `int` 升级到 `bigint` ([d05c376](https://github.com/miaoxing/plugin/commit/d05c376a22f3cb4deb950022236c72b9fcf980c0))


### BREAKING CHANGES

* **app:** 应用编号改为字符串，默认读取第一个应用
* 数据表 `app_id` 字段由 `int` 升级到 `bigint`

## [0.7.1](https://github.com/miaoxing/plugin/compare/v0.7.0...v0.7.1) (2022-02-28)


### Features

* **file:** 增加存储系统基类和本地存储系统的实现 ([5771cb1](https://github.com/miaoxing/plugin/commit/5771cb1026645c7ba7e1fa7032819d50419aafef))
* **Fs:** 增加 `Fs` 服务，用于处理文件操作 ([ccc1cd1](https://github.com/miaoxing/plugin/commit/ccc1cd18b398ab4de7dbb071a5dff008dab2e051))
* **Fs:** 增加 `stripPublic` 方法 ([9ebdb38](https://github.com/miaoxing/plugin/commit/9ebdb3861ff77eb0fa78d17b594ad18ae946938e))
* **Storage:** 增加 `moveLocal` 方法，用于将本地文件写入到文件系统中并删除原来的文件 ([613455d](https://github.com/miaoxing/plugin/commit/613455d30541d9c9c404607677d3cd0fbe130530))
* **Storage:** 增加 `Storage` 服务 ([fb073a2](https://github.com/miaoxing/plugin/commit/fb073a226dce32fd8e62b8a724b34a34b4f5b623))
* **Upload:** Upload 服务增加图片扩展名配置和快速上传图片方法 ([acd6ef3](https://github.com/miaoxing/plugin/commit/acd6ef36a96c382802f94c9b2270256d6ad7e690))





### Dependencies

* **@wei/wei:** upgrade from `0.12.0` to `0.12.1`

# [0.7.0](https://github.com/miaoxing/plugin/compare/v0.6.0...v0.7.0) (2022-02-05)


### Bug Fixes

* **plugin, CastTrait:** `bigint` 类型默认值由字符串 "0" 改为空字符串 ([db8926e](https://github.com/miaoxing/plugin/commit/db8926e993cfba77a57841344a2b73d8c356db90))


### Code Refactoring

* **Config:** 写入本地配置改为 `updateLocal` 方法，移除原有的实现 ([63d5d6a](https://github.com/miaoxing/plugin/commit/63d5d6af1faced7cec5c09ea117ec91ff010daf8))


### Features

* **Config:** 初始化 `Config` 服务 ([63e97f9](https://github.com/miaoxing/plugin/commit/63e97f9d344d10f5a272d8d7eba7ba03c191a280))
* **Config:** 增加 `createService` 和 `getService`，用于结合应用配置创建服务 ([ab5d439](https://github.com/miaoxing/plugin/commit/ab5d439b6c5f10cb62636f2d339091f4914cc1e8))
* **Config:** 增加 `publishPreload` 和 `getPreloadVersionKey` 方法用于管理预加载配置 ([8cbda59](https://github.com/miaoxing/plugin/commit/8cbda59ba3cf16716f4dfa44a11e036da5386b50))
* **Config:** 完善 Config 服务 ([dbbcda6](https://github.com/miaoxing/plugin/commit/dbbcda669c200c665b5ac6514f116a0193c9c9eb))
* **Config, experimental:** 增加预加载全局配置功能 ([640d4ed](https://github.com/miaoxing/plugin/commit/640d4edd3e2940732fc4c3ea778dac1bc99425ab))
* **plugin:** 初始化配置数据表和模型 ([1582a94](https://github.com/miaoxing/plugin/commit/1582a94dc53055729106e131557331b80d48ab14))


### BREAKING CHANGES

* **Config:** 写入本地配置改为 `updateLocal` 方法，移除原有的实现
* **plugin, CastTrait:** `bigint` 类型默认值由字符串 0 改为空字符串





### Dependencies

* **@miaoxing/dev:** upgrade from `8.0.1` to `8.1.0`
* **@mxjs/cli:** upgrade from `0.1.4` to `0.1.5`
* **@wei/wei:** upgrade from `0.11.1` to `0.12.0`

# [0.6.0](https://github.com/miaoxing/plugin/compare/v0.5.0...v0.6.0) (2022-01-12)


### Code Refactoring

* **plugin:** 移除 `AssetLink` 命令，可通过 Webpack CopyPlugin 实现一样的功能 ([b04355d](https://github.com/miaoxing/plugin/commit/b04355daa94ae8f18ad47a172729726d18613c00))


### Features

* **plugin:** `QueryBuilder` 增加 `whereIf` 方法 ([a84a9a9](https://github.com/miaoxing/plugin/commit/a84a9a955b28db2a4c05a70822b12f8eeb857eb1))
* **plugin:** Preflight 允许 `X-Requested-With` 头 ([0265250](https://github.com/miaoxing/plugin/commit/02652507b47e913c9c32add6506c872e2eb77179))


### BREAKING CHANGES

* **plugin:** 移除 `AssetLink` 命令，可通过 Webpack CopyPlugin 实现一样的功能





### Dependencies

* **@miaoxing/dev:** upgrade from `8.0.0` to `8.0.1`
* **@mxjs/cli:** upgrade from `0.1.3` to `0.1.4`
* **@wei/wei:** upgrade from `0.11.0` to `0.11.1`

# [0.5.0](https://github.com/miaoxing/plugin/compare/v0.4.7...v0.5.0) (2021-10-28)


### Bug Fixes

* **BaseTestCase:** `getModelServiceMock` 时允许调用构造函数，解决获取字段值失败 ([10f90dd](https://github.com/miaoxing/plugin/commit/10f90dde7ff84d88c9ee981913b4c19b442159e6))
* **BaseTestCase:** 允许 mock 直接继承`\Wei\Base` 的服务 ([d4a76e9](https://github.com/miaoxing/plugin/commit/d4a76e9fbeebed5a0777c0d0e1a4d48cb12caa5c))
* **BaseTestCase:** 解决 mock 服务不可用 ([0675c2e](https://github.com/miaoxing/plugin/commit/0675c2ef93059ed30bec09fa3c9218a75a639f04))
* **Cast:** `decimal` 字段转换为 PHP 变量时，由浮点数改为字符串 ([b8eb290](https://github.com/miaoxing/plugin/commit/b8eb2902dd12f218ec9808ba28394915b7655c0b))
* **Cast:** `decimal` 字段转换为 PHP 变量时，由浮点数改为字符串 ([58ed5b5](https://github.com/miaoxing/plugin/commit/58ed5b556efb4b6c16bee8b979fca2fe10a1fc2a))
* **CastTrait:** `object` 类型允许传入字符串，会转换为对象 ([3bb852d](https://github.com/miaoxing/plugin/commit/3bb852d937f08c51c5c9e0b13b3b8ffd0bd1e1f6))
* **Config:** 构造函数的参数允许为空数组 ([5c4ab1f](https://github.com/miaoxing/plugin/commit/5c4ab1f41ebb365a1d2f53bd934feb41f35342b2))
* **Model:** `toArray` 指定了返回字段，则不附加虚拟列和关联对象 ([23f6c24](https://github.com/miaoxing/plugin/commit/23f6c2438c924317e41649be6337bca214e445ef))
* **plugin, Model:** `bigint` 字段转换为字符串，解决 js 不支持 19 位以上数字的问题 ([5ba7dd6](https://github.com/miaoxing/plugin/commit/5ba7dd6a95a7946de246ff07d05dba406fd61e21))
* **plugin, Ret:** 调用 `err([code => xx, message => xx])` 时无需生成 `code` ([fbe5b0a](https://github.com/miaoxing/plugin/commit/fbe5b0a82f7adb7f16ea70f828fb3c1814f6a1f4))
* **Ret:** 插件未定义 `code`，则不生成错误码 ([9a009ac](https://github.com/miaoxing/plugin/commit/9a009acc9813aecbaa35c71c9149f22ae21e1cd7))
* **Ret:** 通过 `view` 服务调用 `block` 服务，以免未配置 `block` 服务出错 ([bd50ebd](https://github.com/miaoxing/plugin/commit/bd50ebd5f660a7b5c9127eafdc6aa9e88686dc69))
* **Seeder:** 构造函数的参数允许为空数组 ([ffa6acb](https://github.com/miaoxing/plugin/commit/ffa6acb87c88c05e624545343878125eac630af3))


### Features

* **App:** `setModel` 允许传入 `null` 值清除已有模型 ([b04a85e](https://github.com/miaoxing/plugin/commit/b04a85e6d2ba05e58c3d026f3096545f3ebdf8ee))
* **App, internal:** 支持跨域请求 ([44b454c](https://github.com/miaoxing/plugin/commit/44b454cb3413e55cbe2e6d61aa3a210867c2eadb))
* **BaseTestCase:** 增加 `assertSameRet` 方法 ([3aaeb62](https://github.com/miaoxing/plugin/commit/3aaeb62da7dc79a30e9c2009a7f6f98fda146eec))
* **Cast:** 增加 `intString` 类型，将 `bigint` 转换为 `intString` ([b892015](https://github.com/miaoxing/plugin/commit/b8920151f00dd3e73f14356346039be30501e9d8))
* **MineTrait:** 增加 `MineTrait`，用于查询当前用户的记录 ([a73d0d0](https://github.com/miaoxing/plugin/commit/a73d0d08da36586263c8475ddf22982ac2c15c2e))
* **Model:** `findOrInitBy` 方法允许不传参数 ([88ac57a](https://github.com/miaoxing/plugin/commit/88ac57ab6ddae74bc0330f438e9b74392ac7d3df))
* **Model:** `toRet` 支持传入 Resource 类名和对象 ([f20c0d3](https://github.com/miaoxing/plugin/commit/f20c0d36eb7f21d7fc9a3276343760d5d825c345))
* **Model:** 增加 `destroyOrFail` 方法 ([6f58b7f](https://github.com/miaoxing/plugin/commit/6f58b7ff3c0f7d218dade71f0b2c7d97345702b3))
* **plugin:** `Snowflake` 服务通过缓存服务确保序列数不重复 ([3821ede](https://github.com/miaoxing/plugin/commit/3821ede28bea566dabbb66cc9795187947fc7927))
* **plugin:** 增加 `Snowflake` 服务 ([fde8232](https://github.com/miaoxing/plugin/commit/fde8232aa11da3820621dbcc2284c8c8998fee5b))
* **plugin:** 增加 `SnowflakeTrait`，用于插入数据库前生成 snowflake id ([03b2ab2](https://github.com/miaoxing/plugin/commit/03b2ab262d1b950efc593a42c1709e96c2e537e5))
* **plugin:** 用户 id 字段由 `uInt` 改为 `uBigInt` 类型 ([b7ef11b](https://github.com/miaoxing/plugin/commit/b7ef11b56648a373268da38d4875b49d5d5120bf))
* 数据表主键 由 `int` 改为 `bigint` ([3f6d3b7](https://github.com/miaoxing/plugin/commit/3f6d3b7719738401d1cf06f2b96f0cc1c93e9b4e))
* 模型通过 `SnowflakeTrait` 生成 id ([11f52c1](https://github.com/miaoxing/plugin/commit/11f52c1dd447a87e7cc705100d2eb947f02e13c1))
* **plugin, internal:** 对任意 "-api" 结尾的请求路径返回 JSON 数据 ([d2d4110](https://github.com/miaoxing/plugin/commit/d2d41108b80d33ad10c012785c6412c676095c59))
* **RelationTrait:** 单个模型对象支持调用 `load` 方法来加载关联模型 ([9259b54](https://github.com/miaoxing/plugin/commit/9259b5426eff91d6994f30d87c4335aedb66c4a8))
* **RelationTrait:** 增加 `isLoaded` 方法，用于判断关联对象是否已加载 ([10ace6d](https://github.com/miaoxing/plugin/commit/10ace6da048b4b4f46986e4db915eec17e1b8d6b))
* **RelationTrait:** 更改 `setRelationValue` 方法为 `public`，允许外部设置关联值 ([8e1c755](https://github.com/miaoxing/plugin/commit/8e1c755bf447c6899fe2e0c1e70448d46bd23ca1))
* **Resource:** 增加 `Resource` 功能，用于将模型对象转换为接口响应数组，参考自 Laravel ([c910280](https://github.com/miaoxing/plugin/commit/c9102802e36598bd10480c02376d3f7d7983c66e))
* **Resource:** 支持调用 `includes` 为组合添加额外数据 ([267b28c](https://github.com/miaoxing/plugin/commit/267b28c64ccf917ba2843218f2f0ea03413b1bcf))
* **Tester:** `post` 方法改为 `data`，增加 `post` 方法表示发送 `POST` 请求 ([955c534](https://github.com/miaoxing/plugin/commit/955c5347fa0adbb576cb0866b94ec36e8d9154a3))


### BREAKING CHANGES

* **App:** `App::setModel` 参数改为可选
* **plugin:** 用户 id 字段由 `uInt` 改为 `uBigInt` 类型
* 数据表主键 由 `int` 改为 `bigint`
* **plugin, Model:** `bigint` 字段转换为字符串，解决 js 不支持 19 位以上数字的问题
* **Tester:** Tester 服务的 `post` 方法改为 `data`
* **Cast:** `decimal` 字段转换为 PHP 变量时，由浮点数改为字符串
* **Cast:** `decimal` 字段转换为 PHP 变量时，由浮点数改为字符串
* **Model:** `toRet` 方法的参数去掉 `array` 类型
* **RelationTrait:** 更改 `setRelationValue` 方法为 `public`，允许外部设置关联值





### Dependencies

* **@miaoxing/dev:** upgrade from `7.0.1` to `8.0.0`
* **@mxjs/cli:** upgrade from `0.1.2` to `0.1.3`
* **@wei/wei:** upgrade from `0.10.11` to `0.11.0`

## [0.4.7](https://github.com/miaoxing/plugin/compare/v0.4.6...v0.4.7) (2021-05-21)





### Dependencies

* **@wei/wei:** upgrade from `0.10.10` to `0.10.11`

## [0.4.6](https://github.com/miaoxing/plugin/compare/v0.4.5...v0.4.6) (2021-05-12)





### Dependencies

* **@miaoxing/dev:** upgrade from `7.0.0` to `7.0.1`
* **@mxjs/cli:** upgrade from `0.1.1` to `0.1.2`
* **@wei/wei:** upgrade from `0.10.9` to `0.10.10`

## [0.4.5](https://github.com/miaoxing/plugin/compare/v0.4.4...v0.4.5) (2021-05-11)


### Features

* **plugin:** 使用 `composer/installers` 安装 `miaoxing` 插件到 `plugins` 目录 ([4b0c9bd](https://github.com/miaoxing/plugin/commit/4b0c9bd4bad3ba95e3e8c30367156f1433afcbc4))
* **tester:** 允许静态调用 `Tester::query` 方法 ([11ccc90](https://github.com/miaoxing/plugin/commit/11ccc906d5b514b1c7f8947fc1242346c10f2659))





### Dependencies

* **@miaoxing/dev:** upgrade from `6.4.0` to `7.0.0`
* **@mxjs/cli:** upgrade from `0.1.0` to `0.1.1`
* **@wei/wei:** upgrade from `0.10.8` to `0.10.9`

## [0.4.4](https://github.com/miaoxing/plugin/compare/v0.4.3...v0.4.4) (2021-04-27)


### Bug Fixes

* **Tester:** 每次调用时创建新对象 ([86db4b4](https://github.com/miaoxing/plugin/commit/86db4b490793dc67723d930f73844ecaa21cd01f))


### Features

* 增加创建页面测试数据的 Seeder ([bf9bc9a](https://github.com/miaoxing/plugin/commit/bf9bc9a1bb886a2ad7fc21bd0513f417f62a2a2a))
* **Cast:** 支持转换数据为 `object` 类型 ([2d0b948](https://github.com/miaoxing/plugin/commit/2d0b948bc316670c1ebb2112a029bf3354146e20))
* **Coll:** `saveColl` 支持数组中的数据为对象 ([75fa975](https://github.com/miaoxing/plugin/commit/75fa975860edc35806ffe5a79bac39c461d13bda))
* **experimental, ObjectReq:** 增加 `ObjectReq` 服务，用于以对象的形式获取请求数据 ([c5cff75](https://github.com/miaoxing/plugin/commit/c5cff751a47972dbd71d0eab70cb28cd60e14229))
* **GMetadata:** 支持识别出 `object` 类型 ([e675384](https://github.com/miaoxing/plugin/commit/e6753843b1074b7d464ff64d8548f3deec1386f3))
* **Seeder:** `seeder:run` 命令增加可选参数，允许指定运行的 Seeder 名称 ([7c84e73](https://github.com/miaoxing/plugin/commit/7c84e733f8c2573c38330101f2f246458ebafdb4))
* **Seeder:** `seeder:run` 命令增加可选项 `--form`，允许从哪个 Seeder 开始运行 ([5f71530](https://github.com/miaoxing/plugin/commit/5f71530aa0bd415469e6cea9e436cbad89299d9c))
* **Seeder:** 增加 `g:seeder` 命令为插件生成 Seeder ([6e06cd4](https://github.com/miaoxing/plugin/commit/6e06cd496b5d49fe01a4cde4f65e8ffe76ae80d3))
* **Seeder:** 增加 `seeder:run` 命令来运行 Seeder ([0428879](https://github.com/miaoxing/plugin/commit/04288798e691454bb84f83ee2f90467d5540dc2d))
* **Seeder:** 增加 `seeder:status` 命令来查看 Seeder 运行状态 ([3e6a0c2](https://github.com/miaoxing/plugin/commit/3e6a0c291cd9954994629546545b6f9ab58bbfed))
* **Tester:** 增加 `put` 和 `putAdminApi` 方法 ([4bd6cc4](https://github.com/miaoxing/plugin/commit/4bd6cc409e8dcfae26e27b9a97d51010f7f1d9cd))
* **Tester:** 增加 `setReq` 方法 ([7e84d39](https://github.com/miaoxing/plugin/commit/7e84d3989ae586ab841e9ce87c19d541d7c808d8))





### Dependencies

* **@miaoxing/dev:** upgrade from `6.3.4` to `6.4.0`
* **@wei/wei:** upgrade from `0.10.7` to `0.10.8`

## [0.4.3](https://github.com/miaoxing/plugin/compare/v0.4.2...v0.4.3) (2021-03-22)


### Features

* **Tester:** `get` 方法允许静态调用 ([7073706](https://github.com/miaoxing/plugin/commit/70737062af3162c53a90ff888cf7bd880fd0ae32))
* 增加 `watch-php` 命令，用于监听文件更改，更新插件缓存，事件缓存和自动完成文档 ([395b79a](https://github.com/miaoxing/plugin/commit/395b79ae34b38763a8cf5d86adf71b764f1279a1))





### Dependencies

* **@miaoxing/dev:** upgrade from `6.3.3` to `6.3.4`
* **@wei/wei:** upgrade from `0.10.6` to `0.10.7`

## [0.4.2](https://github.com/miaoxing/plugin/compare/v0.4.1...v0.4.2) (2021-03-12)





### Dependencies

* **@miaoxing/dev:** upgrade from `6.3.2` to `6.3.3`
* **@wei/wei:** upgrade from `0.10.5` to `0.10.6`

## [0.4.1](https://github.com/miaoxing/plugin/compare/v0.4.0...v0.4.1) (2021-03-10)


### Bug Fixes

* **JwtAuth:** 解决调用 `login` 后再调用 `isLogin` 返回 `false` ([85b1773](https://github.com/miaoxing/plugin/commit/85b1773fb2f297f4eef4bdd686ed69e45b41d6d4))
* **User:** 加载外部的数据，要将记录更改为已存在 ([74a1b52](https://github.com/miaoxing/plugin/commit/74a1b52be60962d104b1dc72b0fd0da9e93869dc))


### Features

* **PageRouter:** 增加识别 `pages` 目录下的页面 ([edb01b3](https://github.com/miaoxing/plugin/commit/edb01b31bb2ce56d50d627cb54249ff59dc473ad))





### Dependencies

* **@miaoxing/dev:** upgrade from 6.3.1 to 6.3.2
* **@wei/wei:** upgrade from 0.10.4 to 0.10.5

# [0.4.0](https://github.com/miaoxing/plugin/compare/v0.3.3...v0.4.0) (2021-03-09)


### Bug Fixes

* **App:** `App::getIdByDomain` 返回值由 `string` 改为 `int` ([9108bf7](https://github.com/miaoxing/plugin/commit/9108bf7e4e6d74c0de52533c7e9dacc4d386fad5))
* **Model:** 解决错误调用了模型的 protected 方法 ([935e05a](https://github.com/miaoxing/plugin/commit/935e05a044bc26a7409ea6a05e4e92e96b3874d9))


### BREAKING CHANGES

* **Model:** 模型的 `getRelationModel`，`setRelation`，`getRelation`，`getModelBaseName`，`setAttributesFromDb` 由 `protected` 改为 `public`，并加上 `@internal` 标识
* **App:** `App::getIdByDomain` 返回值由 `string` 改为 `int`





### Dependencies

* **@miaoxing/dev:** upgrade from 6.3.0 to 6.3.1
* **@wei/wei:** upgrade from 0.10.3 to 0.10.4

## [0.3.3](https://github.com/miaoxing/plugin/compare/v0.3.2...v0.3.3) (2021-03-09)


### Bug Fixes

* 子类方法的返回值未兼容 ([9cc9e03](https://github.com/miaoxing/plugin/commit/9cc9e0335c7155808e5b7ab81faac80751aeba73))


### Features

* 兼容 Composer 2 ([7594996](https://github.com/miaoxing/plugin/commit/7594996c094026827ae7a1622b663d9636551f8a))
* 支持 `lcobucci/jwt` 3.4 ([65e0a53](https://github.com/miaoxing/plugin/commit/65e0a5343cc40fd75bd44f1022955178a0fa3223))





### Dependencies

* **@miaoxing/dev:** upgrade from 6.2.0 to 6.3.0
* **@wei/wei:** upgrade from 0.10.2 to 0.10.3

## [0.3.2](https://github.com/miaoxing/plugin/compare/v0.3.1...v0.3.2) (2021-03-05)





### Dependencies

* **@wei/wei:** upgrade from 0.10.1 to 0.10.2

## [0.3.1](https://github.com/miaoxing/plugin/compare/v0.3.0...v0.3.1) (2021-03-05)





### Dependencies

* **@miaoxing/dev:** upgrade from 6.1.2 to 6.2.0
* **@wei/wei:** upgrade from 0.10.0 to 0.10.1

# [0.3.0](https://github.com/miaoxing/plugin/compare/v0.2.4...v0.3.0) (2021-03-05)


### Bug Fixes

* 更新模型类名 ([06542ee](https://github.com/miaoxing/plugin/commit/06542eebd5831937f7fae7695796d28ed4870cc5))
* **AppModel:** 调用不存在的方法导致错误 ([f3999e5](https://github.com/miaoxing/plugin/commit/f3999e5c17811990d7e01b854e9f1fad501e651d))
* **GMetadata:** 解决获取 trait 时缺少依赖的 trait 的问题 ([31f4e13](https://github.com/miaoxing/plugin/commit/31f4e13231d82c94d0e74f04874128f36251931b))
* **GMetadata:** 转换数据库 `bigint` 类型为 PHP `int` ([6bbe192](https://github.com/miaoxing/plugin/commit/6bbe1922fcd28a59c6a97699fa77b9b30b87b5b1))
* **GModel:** 更重新生成类的继承父类 ([25f8f0d](https://github.com/miaoxing/plugin/commit/25f8f0d38b2a0748255f4cb0cb4333b9e7b9575f))
* **Model:** `DefaultScope` 参数顺序错误 ([c189226](https://github.com/miaoxing/plugin/commit/c189226372a8c9da1378925de2fd6ba426af768f))
* **Model:** `findOrCreate` 只有找不到记录才保存 ([817e0c3](https://github.com/miaoxing/plugin/commit/817e0c35c823c56334247e993257ff817d9a3870))
* **Model:** `reload` 之后主键的值变为字符串 ([db2cca3](https://github.com/miaoxing/plugin/commit/db2cca34652a3c53383c6d3708d64b046253ec2d))
* **Model:** 加载嵌套一对多关联错误 ([e88c785](https://github.com/miaoxing/plugin/commit/e88c785da5ecc565942a1d0f64b3f254dac366c4))
* **Model:** 调用 `method_exists` 前检查方法是字符串 ([ec19578](https://github.com/miaoxing/plugin/commit/ec195789051f02bc89eae401ec55268eb03cbc40))
* **Model:** 转换 `list` 类型时，将 `null` 和 空字符串转换为空数组 ([dce1c1f](https://github.com/miaoxing/plugin/commit/dce1c1f06987595040a5d55ac5289effc2384560))
* **queryBuilder:** 调用 `getRawSql` 时，缺少表名出错 ([41fe069](https://github.com/miaoxing/plugin/commit/41fe069eba16242f554447eb87fa6fd45224c812))
* Model fromArray 要忽略关联的模型 ([0c233e3](https://github.com/miaoxing/plugin/commit/0c233e378a8d3e7a4e1c423bf954f75abf91f5b0))
* 数据库日期字段插入空字符出错 ([77f92bc](https://github.com/miaoxing/plugin/commit/77f92bc0f2a943f7ed0554cec659542e00b5c4f1))
* 模型类未定义 casts 属性会提示错误 ([574876f](https://github.com/miaoxing/plugin/commit/574876f722c864d2af937539880374c7a9580d48))
* 生成 wei 的自动加载类时，忽略抽象类，解决代码提示失效 ([9cd4f51](https://github.com/miaoxing/plugin/commit/9cd4f5111e3401b3461e65e666c84116a7caafbd))
* 解决 Model beforeXxx 中获取数据时，数据已经被转换为数据库字符串的问题 ([66eee80](https://github.com/miaoxing/plugin/commit/66eee80bb2b14d4d5c130d8ef918d977ffbfda34))

### Code Refactoring

* **Model:** `isNew` 属性改为 `new` ([0e0197f](https://github.com/miaoxing/plugin/commit/0e0197f4626b58a8d7ab65b27e1edd90223f9f5d))
* **Model:** 重命名 `events` 属性为 `modelEvents`，避免属性和关联模型名称冲突 ([b020827](https://github.com/miaoxing/plugin/commit/b020827531cc9daa7e43558e6c13e84ca5aa5d51))
* **Plugin:** 移除废弃 `getAppControllerMap` 方法，改用 `pageRouter` 服务 ([eb76141](https://github.com/miaoxing/plugin/commit/eb76141d63309f2e9f7af95ad6352e8d579f9405))
* **Plugin:** 移除废弃的 `generateClassMap` 方法，改用 `classMap` 服务的 `generate()` 方法 ([bfd0116](https://github.com/miaoxing/plugin/commit/bfd01162510e4f366bf599c1609f0cb024147c98))
* **reqQuery:** 搜索改为从 `search` 参数中读取，避免和其他参数冲突 ([fec9086](https://github.com/miaoxing/plugin/commit/fec908603180bcf9881eec0cac02dd812b356397))
* **ReqQueryTrait:** 移除废弃的 `setRequest` 方法，改用 `setReq` ([309938f](https://github.com/miaoxing/plugin/commit/309938faf36d7f9b2c3fc812f3accea6c5e32972))
* **reqQuery:** 更改方法名称，移除不适合的方法 ([0150692](https://github.com/miaoxing/plugin/commit/01506927d77c729e1d3bc44cec202e50018cae3b))
* **app:** 改为使用 `id` 作为应用标识 ([3ca0758](https://github.com/miaoxing/plugin/commit/3ca07584ba5fca0bdce942b04b877dacb30a957d))
* **plugin:** 存储插件编号由 JSON 格式改为逗号隔开 ([6b67d66](https://github.com/miaoxing/plugin/commit/6b67d66ea06d807915e9c0c66c2b708d57c0a4d9))
* **Model:** 整理属性转换逻辑，通过 `setAttributeFrom*` 和 `convertTo*Attribute` 方法处理属性 ([b4e7a46](https://github.com/miaoxing/plugin/commit/b4e7a46cc3e727e721d9242da27a47dcc20ea219))
* **Model:** 模型 `__invoke` 方法整理 ([c680dd9](https://github.com/miaoxing/plugin/commit/c680dd9be75d2a3975ecf5afb49645db787651d9))
* **Model:** `CastTrait` 和 `DefaultScopeTrait` 作为内置功能加入 `ModelTrait` 中 ([c13200f](https://github.com/miaoxing/plugin/commit/c13200f39b842c4be834ddcf465b19861ba3346d))
* **Model:** 调整关联相关逻辑 ([e5a9f20](https://github.com/miaoxing/plugin/commit/e5a9f20a8d505621cf67612821f8748695e8886b))
* **Model:** 整理事件相关逻辑 ([199ae29](https://github.com/miaoxing/plugin/commit/199ae29d18b94efc1c1239de42c3215e64edb84e))
* **Model:** 整理服务相关逻辑 ([ec3baf7](https://github.com/miaoxing/plugin/commit/ec3baf7983b33c9e85031c197ece5c7425387210))
* **Model:** 整理模型方法 ([d753ab4](https://github.com/miaoxing/plugin/commit/d753ab4fda472fabe040f451d3535e97ddcec014))
* **Test:** `assertRetErr` 的 `code` 和 `message` 参数对换 ([ccd308c](https://github.com/miaoxing/plugin/commit/ccd308ceb260e87115cb269cb86be419a419f701))
* **Model:** 方法增加类型和返回值 ([4da0bf7](https://github.com/miaoxing/plugin/commit/4da0bf7ab2aa8c8a497d2c78a737172cba9c28ca))
* **Model:** 关联逻辑整理 ([9a01667](https://github.com/miaoxing/plugin/commit/9a01667b7e72a554d53647217a2490acf6fe4ead))
* **Model:** `changes` 属性逻辑整理 ([92cbe14](https://github.com/miaoxing/plugin/commit/92cbe14cbc85e4f641ee7d1f934195e5a9064bfe))
* **Model:** `preExecute` 事件改为 `beforeExecute`，`preBuildQuery` 事件改为 `beforeAddQueryPart` ([1357e09](https://github.com/miaoxing/plugin/commit/1357e091a4f00f4ff8a8485c117effd129ec6bbf))
* **Model:** 整理获取和设置属性的逻辑 ([3e16fd6](https://github.com/miaoxing/plugin/commit/3e16fd6a9a13de2fcbb06baea1bc8b427f219ce4))
* **Model:** `getColumns` 方法改为返回二级数组，键名为数据表字段名，值为字段配置，如 `cast` ([59886df](https://github.com/miaoxing/plugin/commit/59886df55c51d5dcb9ed8f856f523fb4ef4ed22f))
* **Model:** 移除历史遗留的 `findOne` 和 `findAllByIds` 方法 ([3f9940c](https://github.com/miaoxing/plugin/commit/3f9940c90030c0ef91d3a6d9cdfa4af993a34000))
* **QueryBuilder:** `add` 方法改名为 `addQueryPart` ([e124904](https://github.com/miaoxing/plugin/commit/e1249047f67802ae1aff1076a5f6ded7e0cc9d86))
* **QueryBuilder:** 整理 `queryParams` 逻辑 ([36e900c](https://github.com/miaoxing/plugin/commit/36e900c4a11564bbfced56956e876f7fa7e7154d))
* **Model:** 移除自动加载和 `loaded` 属性 ([0212a9c](https://github.com/miaoxing/plugin/commit/0212a9c6ad1b5af4d403b3fb955e5c8f615addca))
* **Model:** 拆分出 `CollTrait` ([8c138af](https://github.com/miaoxing/plugin/commit/8c138af163012163f68dff002b1284366d479fb5))
* **Model:** `data` 属性改为 `attributes`，相关属性和方法更新 ([8f74a53](https://github.com/miaoxing/plugin/commit/8f74a53b0308b5301446bc924135d1ed3d4c9795))
* **Model:** 移除 `detach` 和 `isDestroyed` 属性的相关功能 ([148e5d8](https://github.com/miaoxing/plugin/commit/148e5d85155f070eb82ccc06941e94aa8be2105b))
* **Model:** 移除 `isChanged` 属性，直接使用 `isChanged()` 方法 ([6828ed3](https://github.com/miaoxing/plugin/commit/6828ed3bbd74f89f498f77564e02ced43fb10039))
* **QueryBuilder:** 允许直接调用 `setCacheKey` 或 `setCacheTags` 来启用缓存 ([51b6423](https://github.com/miaoxing/plugin/commit/51b64236657d7e027a892560464c5ffb4856bd5e))
* **Model:** 列表返回数据中 `rows` 改为 `limit`，`records` 改为 `total` ([00d1ea7](https://github.com/miaoxing/plugin/commit/00d1ea7a8e42ee6314f4c8989fcdfd399f441093))
* **Model:** Model 列名都改为驼峰格式，与数据库交互转换为下划线格式 ([92be472](https://github.com/miaoxing/plugin/commit/92be47278af2f238bf8d301c7bb1e7b6e58685c1))
* **config:** 默认的数据表前缀加上 `mx_` ([5118532](https://github.com/miaoxing/plugin/commit/5118532a684c11901a357ff9f263b4a71ba9f75c))
* `Service/Model` 拆分出 `ModelTrait`, 改名为 `BaseModel` ([a29bae7](https://github.com/miaoxing/plugin/commit/a29bae796895c78ccf1debee803793c5ba87d80c))
* 同步数据库迁移语句更改 ([18c8597](https://github.com/miaoxing/plugin/commit/18c8597e0ca4ebe5e422417f832782cb51f7b7d7))
* 区域由 `area` 改为 `district` ([3ede966](https://github.com/miaoxing/plugin/commit/3ede966c509caefff939d829a2dda5dadb8e308c))

### Features

* **GAutoCompletion:** 生成代码提示时，同时生成校验器的方法 ([b7f25dd](https://github.com/miaoxing/plugin/commit/b7f25ddb70d90a7038dd31c14d88050ec1ca3632))
* **GMetadata:** 生成 Metadata trait 自动引入 ModelTrait ([ee19724](https://github.com/miaoxing/plugin/commit/ee19724ee5f0b4d53c3697e343a62a42435be8dc))
* **GMetadata:** 生成模型的属性增加 null 类型 ([8806740](https://github.com/miaoxing/plugin/commit/8806740cdfdfa79228aa61b68becc2730cbea540))
* **Model:** `max` 方法允许静态调用 ([6218b8d](https://github.com/miaoxing/plugin/commit/6218b8dcefb1dc1b1ee8ba679b3dddf63423f8e6))
* **Model:** 增加 `__isset` 方法，用于检查属性是否存在 ([4fefca1](https://github.com/miaoxing/plugin/commit/4fefca1988c7718098985983e4d518a78dc9f2a4))
* **Model:** 增加 `__unset` 方法，用于清空属性的值 ([1072b95](https://github.com/miaoxing/plugin/commit/1072b95a49b6cc0ea81bc6ebff66314bfaa545f2))
* **Model:** 增加 `CacheTrait`，包含缓存相关方法 ([aebad37](https://github.com/miaoxing/plugin/commit/aebad37b1c4e7ab57c7193e8cbfe99288523cb21))
* **Model:** 增加 `joinRelation`，`innerJoinRelation`，`leftJoinRelation` 和 `rightJoinRelation` 方法用于创建关联查询 ([68d99c3](https://github.com/miaoxing/plugin/commit/68d99c3239ab4c208e4f94fb21f12be4c79be0ef))
* **Model:** 增加 `wasRecentlyCreated` 方法，用于检查模型是否在当前请求中创建的 ([47becba](https://github.com/miaoxing/plugin/commit/47becbaed4b10d1aa5dd730a4bbd31110d2864f6))
* **Model:** 类型转换增加 getCasts 方法，废弃 $defaultCasts 属性 ([4f9addc](https://github.com/miaoxing/plugin/commit/4f9addc148b33ce6b52f21eb23123c41727da12e))
* **Model:** 类型转换增加 list 类型 ([7c2831d](https://github.com/miaoxing/plugin/commit/7c2831d40cee626b6c4955f18a67d52bcf819500))
* **Model:** 软删除支持额外的自定义列和值 ([37324c5](https://github.com/miaoxing/plugin/commit/37324c51cfeb88fd35d0a7e145a1bc4f5a0d63f7))
* **Model:** 增加自动从数据库读取字段默认值 ([bd4bef5](https://github.com/miaoxing/plugin/commit/bd4bef50b1bd8c081cfc7bb8575ea978bab445fe))
* **Model:** 类型转换将不能为 `null` 的值转换为对应的类型 ([225d392](https://github.com/miaoxing/plugin/commit/225d39271127f58ab94e12c1ab80ec808e499741))
* **Model** 增加 saveRelation 方法，用于关联对象保存数据 ([ce3910f](https://github.com/miaoxing/plugin/commit/ce3910fb4edd09ea230e26a553b70ccf4da970da))
* **Plugin:** 增加 `loadConfig` 方法 ([d0b82e4](https://github.com/miaoxing/plugin/commit/d0b82e44a457b75d3054ae615562cf16078892d7))
* **QueryBuilder:** 增加 `metadataCache` 属性，用于缓存数据表数据 ([9616e64](https://github.com/miaoxing/plugin/commit/9616e64a8c8fdc89d30cabfae21d1ca710a4c114))
* **QueryBuilder:** 查询时如果有 join 关联， where 条件自动加上表名 ([6732f3d](https://github.com/miaoxing/plugin/commit/6732f3d094b02cd7f698185e04a61087f69326d6))
* **reqQuery:** 增加 `setDefaultSortColumn` 和 `setDefaultOrder` 方法用于设置默认排序字段和方向 ([00d2937](https://github.com/miaoxing/plugin/commit/00d29378d5b39565fbba30a4c54891aba36a15d6))
* **reqQuery:** 支持使用 `:` 作为搜索名称和类型的分隔符 ([907cfbd](https://github.com/miaoxing/plugin/commit/907cfbddd2fa9e7205d95f0d9949d415002942f7))
* **reqQuery:** 支持多个字段排序 ([c1144ce](https://github.com/miaoxing/plugin/commit/c1144ce2f368c24d2853acc345face762817fd50))
* **reqQuery:** 支持设置允许排序的字段和顺序 ([68d8869](https://github.com/miaoxing/plugin/commit/68d8869236701b99594fddf7a62a7eb0e9b35ab4))
* **Ret:** 创建模型后，响应自动返回 201 ([0c69310](https://github.com/miaoxing/plugin/commit/0c69310a8d4ebaeffe37162b70227b070b4b5234))
* **Validator:** 增加 IsModelExists 规则，用于校验模型数据是否存在 ([a672104](https://github.com/miaoxing/plugin/commit/a6721044086047670a8e0405a224dd7baa0adcff))
* **reqQuery:** 增加 `setReqSearch` 方法来设置允许搜索的字段条件 ([ccbdf0c](https://github.com/miaoxing/plugin/commit/ccbdf0c0a333f16575b36603033e8fe2a700630b))

### BREAKING CHANGES

* 去除 `reqSearch` 的参数，改用 `setReqSearch` 指定允许搜索的条件
移除 `eq` 操作符，减低逻辑复杂度
整理搜索相关方法的命名
* 更改方法名称，移除不适合的方法
* **reqQuery:** 搜索改为从 `search` 参数中读取，避免和其他参数冲突
* 区域由 `area` 改为 `district`
* 改为使用 `id` 作为应用标识
* 存储插件编号由 JSON 格式改为逗号隔开
* 整理属性转换逻辑，通过 `setAttributeFrom*` 和 `convertTo*Attribute` 方法处理属性
* `QueryBuilder` 移除 `__invoke` 方法，改为使用 `::table` 方法创建新对象
* `CastTrait` 和 `DefaultScopeTrait` 作为内置功能加入 `ModelTrait` 中
* 调整关联相关逻辑
* 整理事件相关逻辑
* 整理服务相关逻辑
* 整理模型方法
* `assertRetErr` 的 `code` 和 `message` 参数对换
* 方法增加类型和返回值
* 关联逻辑整理
* `changes` 属性逻辑整理
* `preExecute` 事件改为 `beforeExecute`，`preBuildQuery` 事件改为 `beforeAddQueryPart`
* 整理获取和设置属性的逻辑
* 调整初始化的顺序；初始化时不再允许传入 db 数据，改为调用 `setDbAttributes`；移除 `afterLoad` 事件
* 类型转换将不能为 `null` 的值转换为对应的类型
* `getColumns` 方法改为返回二级数组，键名为数据表字段名，值为字段配置，如 `cast`
* 移除历史遗留的 `findOne` 和 `findAllByIds` 方法
* `add` 方法改名为 `addQueryPart`
* 整理 `queryParams` 逻辑
* 移除自动加载和 `loaded` 属性
* 拆分出 `CollTrait`
* **Model:** `isNew` 属性改为 `new`
* `data` 属性改为 `attributes`，相关属性和方法更新
* 移除 `detach` 和 `isDestroyed` 属性的相关功能
* 移除 `isChanged` 属性，直接使用 `isChanged()` 方法
* **Model:** 重命名 `events` 属性为 `modelEvents`，避免属性和关联模型名称冲突
* 允许直接调用 `setCacheKey` 或 `setCacheTags` 来启用缓存
* 列表返回数据中 `rows` 改为 `limit`，`records` 改为 `total`
* `Service/Model` 拆分出 `ModelTrait`, 改名为 `BaseModel`
* Model 列名都改为驼峰格式，与数据库交互转换为下划线格式
* 移除 `Schema` 服务，功能已同步到 `Wei\Schema`，直接使用 `Wei\Schema`
* 默认的数据表前缀加上 `mx_`
* **ReqQueryTrait:** 移除废弃的 `setRequest` 方法，改用 `setReq`
* **Plugin:** 移除废弃 `getAppControllerMap` 方法，改用 `pageRouter` 服务
* **Plugin:** 移除废弃的 `generateClassMap` 方法，改用 `classMap` 服务的 `generate()` 方法





### Dependencies

* **@wei/wei:** upgrade from 0.9.31 to 0.10.0

## [0.2.4](https://github.com/miaoxing/plugin/compare/v0.2.3...v0.2.4) (2020-09-27)


### Bug Fixes

* 未登录时获取用户 id 时，调用进入死循环出错 ([8c7a635](https://github.com/miaoxing/plugin/commit/8c7a635e84788f586b1a3d17ebfd38bd7da60424))
* **PageRouter:** 匹配结果可能不含 path 键名 ([246af5d](https://github.com/miaoxing/plugin/commit/246af5d11408fbe0dc6439b36b0a26d0d3512bcf))
* 未登录时获取用户 id 时，调用进入死循环出错 ([15c46e7](https://github.com/miaoxing/plugin/commit/15c46e70992bdcec5c0e632c2164452675e40afb))


### Features

* 增加 getGuarded getFillable getHidden 方法，以便子类继承附加新的字段 ([4a9c64e](https://github.com/miaoxing/plugin/commit/4a9c64e703d1806d64960afaf83a584bc0b2991f))
* 增加 setPrivateKey，setPublicKey 方法 ([7941861](https://github.com/miaoxing/plugin/commit/794186129675446dbe218f554f45d4f1c8bf0d3f))


### Reverts

* "fix: 未登录时获取用户 id 时，调用进入死循环出错" ([8c1da2f](https://github.com/miaoxing/plugin/commit/8c1da2f709927b136b1afa2034ce0af8b05621a9))

## [0.2.3](https://github.com/miaoxing/plugin/compare/v0.2.2...v0.2.3) (2020-09-25)


### Bug Fixes

* date/datetime 自动转换 null 为空字符串导致数据库出错 ([1bb4e50](https://github.com/miaoxing/plugin/commit/1bb4e50edde8c82fa2aae4194ebb5ecf6b61a181))
* 生成 metadata 的数据表缺少前缀 ([42ad445](https://github.com/miaoxing/plugin/commit/42ad445a11422c4fde9de3ec19dcd9a5578d9b71))
* **model:** 多个记录加载多个记录时，关联数据可能变空 ([4b52337](https://github.com/miaoxing/plugin/commit/4b523372088f70a39bbad1ad73b58812e85726c5))


### Features

* ReqQuery 支持数组形式的范围查找 ([367428f](https://github.com/miaoxing/plugin/commit/367428f2414e2ef0d727b9eaaee6572552293afb))
* 允许调用 XxxModel::toArray ([d2e2716](https://github.com/miaoxing/plugin/commit/d2e27162c622eb23f1b643e9908f45f47e0c7091))





### Dependencies

* **@miaoxing/dev:** upgrade from 6.1.1 to 6.1.2
* **@wei/wei:** upgrade from 0.9.30 to 0.9.31

## [0.2.2](https://github.com/miaoxing/plugin/compare/v0.2.1...v0.2.2) (2020-09-06)


### Bug Fixes

* 调用 err(['message' => 'xx']) 时解析 message 错误 ([4a337ae](https://github.com/miaoxing/plugin/commit/4a337aef7c61f43ada0d5c2472de77d05f599d07))


### Features

* 增加 jwt 登录 ([e05b28a](https://github.com/miaoxing/plugin/commit/e05b28a784f3ce9bad4ec22b38a8105be76edf39))
* 安装时生成 jwt 所需的 RSA 密钥对 ([dffd6d3](https://github.com/miaoxing/plugin/commit/dffd6d303d5e48801cdc1e5ff37f7d8251640b69))

## [0.2.1](https://github.com/miaoxing/plugin/compare/v0.2.0...v0.2.1) (2020-09-01)


### Features

* 支持 doctrine/inflector ^1.4|^2.0 ([9bf203a](https://github.com/miaoxing/plugin/commit/9bf203aabbff463f1dd48b473a944c9dca37d1ab))

# [0.2.0](https://github.com/miaoxing/plugin/compare/v0.1.7...v0.2.0) (2020-09-01)


### Bug Fixes

* 路径错误导致路由未识别到 ([4884591](https://github.com/miaoxing/plugin/commit/4884591f2f1023ca7dfcb292f8a01437f4a0f10e))
* **pageRouter:** 相同的页面目录会被覆盖 ([9250a95](https://github.com/miaoxing/plugin/commit/9250a950c6d6a1dda4b1a01480a1034cd78c0745))
* **Ret:** 不在 src 目录下调用解析插件名称错误 ([b9eaee9](https://github.com/miaoxing/plugin/commit/b9eaee9a1b9d831fc4ecd90e76db63b364d24bdd))
* 默认 code 和 Ret 服务保持一致 ([1ca02b8](https://github.com/miaoxing/plugin/commit/1ca02b8cb282f8a67323da0151944d8f9604bdbb))


### Code Refactoring

* 后端控制器改为 page 模式 ([f0e7467](https://github.com/miaoxing/plugin/commit/f0e74677f967fb30aecb17d8dded56df7283abd0))


### Features

* 增加 PageRouter 服务 ([9383e28](https://github.com/miaoxing/plugin/commit/9383e286dbc9d14f52508c04cd0b7b97ab21111e))
* 增加自动生成错误码功能 ([49907ea](https://github.com/miaoxing/plugin/commit/49907eae4442cc430a08a9f61994befe8d24e24d))


### BREAKING CHANGES

* 后端控制器改为 page 模式

## [0.1.7](https://github.com/miaoxing/plugin/compare/v0.1.6...v0.1.7) (2020-08-17)





### Dependencies

* **@miaoxing/dev:** upgrade from 6.1.0 to 6.1.1
* **@wei/wei:** upgrade from 0.9.29 to 0.9.30

## [0.1.6](https://github.com/miaoxing/plugin/compare/v0.1.5...v0.1.6) (2020-08-14)





### Dependencies

* **@miaoxing/dev:** upgrade from 6.0.0 to 6.1.0
* **@wei/wei:** upgrade from 0.9.28 to 0.9.29

## [0.1.5](https://github.com/miaoxing/plugin/compare/v0.1.4...v0.1.5) (2020-08-14)





### Dependencies

* **@miaoxing/dev:** upgrade from  to 0.1.0
* **@wei/wei:** upgrade from 0.9.27 to 0.9.28

## [0.1.4](https://github.com/miaoxing/plugin/compare/v0.1.3...v0.1.4) (2020-08-11)


### Features

* 允许在通过 composer.json 配置预加载的服务 ([3de1fb0](https://github.com/miaoxing/plugin/commit/3de1fb0904ef709ef2241b982ca97a203b10e421))

## [0.1.3](https://github.com/miaoxing/plugin/compare/v0.1.2...v0.1.3) (2020-08-07)


### Features

* 增加 `findFromReq` 和 `setReq` 方法，废弃 `findFromReq` 和 `setRequest` ([8101b5f](https://github.com/miaoxing/plugin/commit/8101b5f9a3c171cc3bfd3c63fc95d7cead35aca3))





### Dependencies

* **@wei/wei:** upgrade from 0.9.26 to 0.9.27

## [0.1.2](https://github.com/miaoxing/plugin/compare/v0.1.1...v0.1.2) (2020-08-06)





### Dependencies

* **@wei/wei:** upgrade from 0.9.25 to 0.9.26

## [0.1.1](https://github.com/miaoxing/plugin/compare/v0.1.0...v0.1.1) (2020-08-01)





### Dependencies

* **@wei/wei:** upgrade from 0.9.24 to 0.9.25

# 0.1.0 (2020-07-30)


### Features

* init
