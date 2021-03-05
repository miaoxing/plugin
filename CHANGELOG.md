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
