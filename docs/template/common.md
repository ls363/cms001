#### 标签体系

### 请求参数标签
{request.字段名} 为请求参数标签，CMS001会从请求URL中解析出对应的class_id 、model_id, 如果是内容详情会有 id。

#### 常用的{request.标签}
```
{request.id} 内容的ID
{request.class_id} 内容分类的ID
{request.model_id} 内容模型的ID
{request.tag} 标签
{request.keywords} 关键字，暂时只能从 title【标题】 与 intro【摘要】 字段，模糊搜索
{request.search_type} 指定的搜索字段，可以是对应表中的字段。
{request.search_text} 搜索的关键字，必须与search_type一起使用。
```
#### 网站配置标签
###### {#字段名#} 这种标签，用于获取系统配置，即system_config表的字段。
```
  {#site_name#}  站点名称
  {#site_home#} 首页名称
  {#site_domain#} 站点域名
  {#site_logo#} 站点Logo的图片数字
  {#site_logo_pic#} 站点Logo的地址
  {#cover#} 内页封面图
  {#coverPicBig#} 内页Banner区域背景原图
  {#coverPic#} 内页Banner区域背景缩略略
  {#copyrights#} 底部版权所有,
  {#statistical_code#} 统计代码
  {#icp#} ICP备案号
  {#skin#} 站点皮肤
  {#seo_title#} '站点SEO标题',
  {#seo_keywords#}'站点SEO关键字',
  {#seo_description#} '站点SEO描述',
  {#protocol#}  免责声明
  {#privacy#}  隐私协议
```

{global.title} 可以通过global参数，将某一条记录，设为全局调用 可以是一个分类，也可以是具体的内容


#### 公司信息标签
###### {companyt.字段名}

```
{company.name}公司名称
{company.address} 公司地址
{company.postcode} 邮编
{company.linkman} 联系人
{company.mobile} 手机号
{company.phone} 电话
{company.fax} 传真
{company.email} 邮箱
{company.qq} QQ
{company.wechat}  微信
{company.license_code}  营业执照号码
{company.remark}  备注
```