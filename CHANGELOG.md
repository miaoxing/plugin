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
