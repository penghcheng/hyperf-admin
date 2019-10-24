/*
Navicat MySQL Data Transfer

Source Server         : 宏-docker33306
Source Server Version : 50728
Source Host           : 120.79.148.142:33306
Source Database       : hyperf-api

Target Server Type    : MYSQL
Target Server Version : 50728
File Encoding         : 65001

Date: 2019-10-24 17:54:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sys_captcha
-- ----------------------------
DROP TABLE IF EXISTS `sys_captcha`;
CREATE TABLE `sys_captcha` (
  `uuid` char(36) NOT NULL COMMENT 'uuid',
  `code` varchar(6) NOT NULL COMMENT '验证码',
  `expire_time` datetime DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统验证码';

-- ----------------------------
-- Records of sys_captcha
-- ----------------------------
INSERT INTO `sys_captcha` VALUES ('05b66c15-3c82-4df5-8d59-3639e7e8211c', 'p5e75', '2019-10-21 16:40:21');
INSERT INTO `sys_captcha` VALUES ('06d4d503-ae31-4f29-8d39-1fd300c8078f', 'ndw3x', '2019-10-18 14:31:40');
INSERT INTO `sys_captcha` VALUES ('0c712c0d-e56a-4c65-8594-64ddd9821f7e', 'xyxp5', '2019-10-22 16:51:23');
INSERT INTO `sys_captcha` VALUES ('1e8e8fe8-de8f-4ced-86ac-44aaf79958b0', 'wc8b2', '2019-10-22 10:09:38');
INSERT INTO `sys_captcha` VALUES ('365d566e-2343-4574-8318-3bf0740c391a', 'nfwn8', '2019-10-18 14:19:34');
INSERT INTO `sys_captcha` VALUES ('47fea502-f607-41d3-8429-3ba69c0a64d9', '28b2m', '2019-10-21 15:06:43');
INSERT INTO `sys_captcha` VALUES ('62ee6c12-1860-42ee-8f84-b6aef559ca2c', 'w4pp6', '2019-10-18 14:19:12');
INSERT INTO `sys_captcha` VALUES ('6b7b7f3f-4511-4f46-8f0d-71c2863eea1f', '8ggyy', '2019-10-21 15:04:00');
INSERT INTO `sys_captcha` VALUES ('8a26cb85-cbfd-46e6-86ba-398a710c3a24', 'c2yfb', '2019-10-21 09:58:12');
INSERT INTO `sys_captcha` VALUES ('a1bd8071-1daa-4647-8628-3e3230cf0264', '4ewcy', '2019-10-22 09:19:28');
INSERT INTO `sys_captcha` VALUES ('b5f84085-c917-45e5-8471-3c5c0e693fa6', '7fgan', '2019-10-18 14:22:07');
INSERT INTO `sys_captcha` VALUES ('c4bbd7fd-0d9b-4648-8434-2a8f084a0ca0', '8wxwc', '2019-10-21 09:30:04');
INSERT INTO `sys_captcha` VALUES ('d0e31491-428b-4a4f-8d76-3f18afb56538', 'nbn24', '2019-10-22 09:19:30');
INSERT INTO `sys_captcha` VALUES ('efa066c0-bf4d-4615-8bb3-5a09d9ae1715', '7a7cf', '2019-10-21 14:40:03');

-- ----------------------------
-- Table structure for sys_config
-- ----------------------------
DROP TABLE IF EXISTS `sys_config`;
CREATE TABLE `sys_config` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `param_key` varchar(50) DEFAULT NULL COMMENT 'key',
  `param_value` varchar(2000) DEFAULT NULL COMMENT 'value',
  `status` tinyint(4) DEFAULT '1' COMMENT '状态   0：隐藏   1：显示',
  `remark` varchar(500) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `param_key` (`param_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='系统配置信息表';

-- ----------------------------
-- Records of sys_config
-- ----------------------------
INSERT INTO `sys_config` VALUES ('1', 'CLOUD_STORAGE_CONFIG_KEY', '{\"aliyunAccessKeyId\":\"\",\"aliyunAccessKeySecret\":\"\",\"aliyunBucketName\":\"\",\"aliyunDomain\":\"\",\"aliyunEndPoint\":\"\",\"aliyunPrefix\":\"\",\"qcloudBucketName\":\"\",\"qcloudDomain\":\"\",\"qcloudPrefix\":\"\",\"qcloudSecretId\":\"\",\"qcloudSecretKey\":\"\",\"qiniuAccessKey\":\"NrgMfABZxWLo5B-YYSjoE8-AZ1EISdi1Z3ubLOeZ\",\"qiniuBucketName\":\"ios-app\",\"qiniuDomain\":\"http://7xqbwh.dl1.z0.glb.clouddn.com\",\"qiniuPrefix\":\"upload\",\"qiniuSecretKey\":\"uIwJHevMRWU0VLxFvgy0tAcOdGqasdtVlJkdy6vV\",\"type\":1}', '0', '云存储配置信息');

-- ----------------------------
-- Table structure for sys_log
-- ----------------------------
DROP TABLE IF EXISTS `sys_log`;
CREATE TABLE `sys_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL COMMENT '用户名',
  `operation` varchar(50) DEFAULT NULL COMMENT '用户操作',
  `method` varchar(200) DEFAULT NULL COMMENT '请求方法',
  `params` varchar(5000) DEFAULT NULL COMMENT '请求参数',
  `time` bigint(20) NOT NULL COMMENT '执行时长(毫秒)',
  `ip` varchar(64) DEFAULT NULL COMMENT 'IP地址',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COMMENT='系统日志';

-- ----------------------------
-- Records of sys_log
-- ----------------------------
INSERT INTO `sys_log` VALUES ('1', 'admin', '保存用户', 'io.renren.modules.sys.controller.SysUserController.save()', '[{\"userId\":2,\"username\":\"peng\",\"password\":\"b9ca25de8086c96ee367a24a624e8be180604db1d64f3def33ef74e986e48820\",\"salt\":\"6pSWAG1NJy5Sy0dw5ODE\",\"email\":\"2865900737@qq.com\",\"mobile\":\"15018445543\",\"status\":1,\"roleIdList\":[],\"createUserId\":1,\"createTime\":\"Oct 18, 2019, 2:16:01 PM\"}]', '242', '127.0.0.1', '2019-10-18 14:16:01');
INSERT INTO `sys_log` VALUES ('2', 'admin', '保存角色', 'io.renren.modules.sys.controller.SysRoleController.save()', '[{\"roleId\":1,\"roleName\":\"test\",\"remark\":\"test\",\"createUserId\":1,\"menuIdList\":[15,18,19,20,21,4,23,24,25,26,5,7,27,29,30,-666666,1,2,3,6],\"createTime\":\"Oct 18, 2019, 2:16:41 PM\"}]', '42', '127.0.0.1', '2019-10-18 14:16:41');
INSERT INTO `sys_log` VALUES ('3', 'admin', '修改用户', 'io.renren.modules.sys.controller.SysUserController.update()', '[{\"userId\":2,\"username\":\"peng\",\"salt\":\"6pSWAG1NJy5Sy0dw5ODE\",\"email\":\"2865900737@qq.com\",\"mobile\":\"15018445543\",\"status\":1,\"roleIdList\":[1],\"createUserId\":1}]', '17', '127.0.0.1', '2019-10-18 14:16:50');
INSERT INTO `sys_log` VALUES ('4', 'admin', '修改角色', 'io.renren.modules.sys.controller.SysRoleController.update()', '[{\"roleId\":1,\"roleName\":\"test\",\"remark\":\"test\",\"createUserId\":1,\"menuIdList\":[15,16,18,19,20,21,4,23,24,25,26,5,7,27,29,30,-666666,1,2,3,6]}]', '22', '127.0.0.1', '2019-10-18 14:19:02');
INSERT INTO `sys_log` VALUES ('5', 'admin', '修改角色', 'io.renren.modules.sys.controller.SysRoleController.update()', '[{\"roleId\":1,\"roleName\":\"test\",\"remark\":\"test\",\"createUserId\":1,\"menuIdList\":[2,15,16,17,18,19,20,21,4,23,24,25,26,5,27,29,30,-666666,1,3]}]', '18', '127.0.0.1', '2019-10-18 14:19:40');
INSERT INTO `sys_log` VALUES ('6', 'admin', '修改用户', 'io.renren.modules.sys.controller.SysUserController.update()', '[{\"userId\":2,\"username\":\"peng\",\"salt\":\"6pSWAG1NJy5Sy0dw5ODE\",\"email\":\"2865900737@qq.com\",\"mobile\":\"15018445543\",\"status\":1,\"roleIdList\":[1],\"createUserId\":1}]', '7', '127.0.0.1', '2019-10-18 14:20:54');
INSERT INTO `sys_log` VALUES ('7', 'admin', '修改角色', 'io.renren.modules.sys.controller.SysRoleController.update()', '[{\"roleId\":1,\"roleName\":\"test\",\"remark\":\"test\",\"createUserId\":1,\"menuIdList\":[2,15,16,17,18,19,20,21,4,23,24,25,26,5,6,7,8,9,10,11,12,13,14,27,29,30,-666666,1,3]}]', '103', '127.0.0.1', '2019-10-18 14:31:14');
INSERT INTO `sys_log` VALUES ('8', 'admin', '保存用户', 'io.renren.modules.sys.controller.SysUserController.save()', '[{\"userId\":3,\"username\":\"test\",\"password\":\"7eed306b25cf247133bfbd7efa88be2902c1498a06fbe94e186eb6f691832b41\",\"salt\":\"4WhrMBTnroXoj1kcXtJA\",\"email\":\"qq@qq.com\",\"mobile\":\"18818818808\",\"status\":1,\"roleIdList\":[1],\"createUserId\":1,\"createTime\":\"Oct 18, 2019, 2:32:09 PM\"}]', '24', '127.0.0.1', '2019-10-18 14:32:09');
INSERT INTO `sys_log` VALUES ('9', 'test', '保存用户', 'io.renren.modules.sys.controller.SysUserController.save()', '[{\"userId\":4,\"username\":\"test_1\",\"password\":\"1a142ff928f7bd431526722a073cfcc4b37fdef2a017f6db7813a3a5e89194e6\",\"salt\":\"XLwrkjUJFpO3DyJF3YQo\",\"email\":\"77@qq.com\",\"mobile\":\"19919919909\",\"status\":1,\"roleIdList\":[],\"createUserId\":3,\"createTime\":\"Oct 18, 2019, 2:33:44 PM\"}]', '5', '127.0.0.1', '2019-10-18 14:33:44');
INSERT INTO `sys_log` VALUES ('10', 'peng', '暂停定时任务', 'io.renren.modules.job.controller.ScheduleJobController.pause()', '[[1]]', '19', '127.0.0.1', '2019-10-18 16:33:26');
INSERT INTO `sys_log` VALUES ('11', 'admin', '删除用户', 'io.renren.modules.sys.controller.SysUserController.delete()', '[[4]]', '19', '0:0:0:0:0:0:0:1', '2019-10-18 17:38:50');
INSERT INTO `sys_log` VALUES ('12', 'peng', '保存角色', 'io.renren.modules.sys.controller.SysRoleController.save()', '[{\"roleId\":3,\"roleName\":\"经纪人\",\"remark\":\"test\",\"createUserId\":2,\"menuIdList\":[2,15,16,17,18,19,20,21,4,23,24,25,26,5,6,7,8,9,10,11,12,13,14,27,29,30,-666666,1,3],\"createTime\":\"Oct 18, 2019, 5:45:51 PM\"}]', '37', '0:0:0:0:0:0:0:1', '2019-10-18 17:45:51');
INSERT INTO `sys_log` VALUES ('13', 'peng', '保存用户', 'io.renren.modules.sys.controller.SysUserController.save()', '[{\"userId\":5,\"username\":\"jingjiren\",\"password\":\"6037c85336e58e3a8be51ea5756b11941a8cc91917a8832c9228c9a57e4663ea\",\"salt\":\"TaVs6zIvs6GTbJ4ZZXXr\",\"email\":\"ww@qq.com\",\"mobile\":\"19919919901\",\"status\":1,\"roleIdList\":[3],\"createUserId\":2,\"createTime\":\"Oct 18, 2019, 5:46:30 PM\"}]', '54', '0:0:0:0:0:0:0:1', '2019-10-18 17:46:30');
INSERT INTO `sys_log` VALUES ('14', 'admin', '修改角色', 'io.renren.modules.sys.controller.SysRoleController.update()', '[{\"roleId\":3,\"roleName\":\"经纪人\",\"remark\":\"test\",\"createUserId\":1,\"menuIdList\":[2,15,16,17,18,19,20,21,4,23,24,25,26,5,7,9,11,12,13,14,27,29,30,-666666,1,3,6]}]', '46', '0:0:0:0:0:0:0:1', '2019-10-18 17:48:32');
INSERT INTO `sys_log` VALUES ('15', 'admin', '保存菜单', 'io.renren.modules.sys.controller.SysMenuController.save()', '[{\"menuId\":31,\"parentId\":0,\"name\":\"调查管理\",\"type\":0,\"icon\":\"log\",\"orderNum\":3}]', '7', '0:0:0:0:0:0:0:1', '2019-10-18 17:56:56');
INSERT INTO `sys_log` VALUES ('16', 'admin', '删除菜单', 'io.renren.modules.sys.controller.SysMenuController.delete()', '[31]', '0', '0:0:0:0:0:0:0:1', '2019-10-18 17:57:44');
INSERT INTO `sys_log` VALUES ('17', 'admin', '删除菜单', 'io.renren.modules.sys.controller.SysMenuController.delete()', '[31]', '0', '0:0:0:0:0:0:0:1', '2019-10-18 17:57:48');
INSERT INTO `sys_log` VALUES ('18', 'admin', '删除菜单', 'io.renren.modules.sys.controller.SysMenuController.delete()', '[31]', '0', '0:0:0:0:0:0:0:1', '2019-10-18 17:57:54');
INSERT INTO `sys_log` VALUES ('19', 'peng', '保存用户', 'io.renren.modules.sys.controller.SysUserController.save()', '[{\"userId\":6,\"username\":\"phc\",\"password\":\"a9ad75608391e60866fc8b1c18f889ced4e3ba4da32538973f5cf26f85c9f05a\",\"salt\":\"Y8O65qirXIGqtR1h6yug\",\"email\":\"ww@qq.com\",\"mobile\":\"18818818817\",\"status\":1,\"roleIdList\":[],\"createUserId\":2,\"createTime\":\"Oct 22, 2019, 2:09:57 PM\"}]', '302', '0:0:0:0:0:0:0:1', '2019-10-22 14:09:57');
INSERT INTO `sys_log` VALUES ('20', 'admin', '保存用户', 'io.renren.modules.sys.controller.SysUserController.save()', '[{\"userId\":7,\"username\":\"test01\",\"password\":\"ad86fd89beec95af1ee49f7e5dfadeb1060a913989c504a7b6c12b38922924ce\",\"salt\":\"dYMdhl0YUM4ztTEnIpLF\",\"email\":\"test01@qq.com\",\"mobile\":\"15515515505\",\"status\":1,\"roleIdList\":[3],\"createUserId\":1,\"createTime\":\"Oct 22, 2019, 2:16:38 PM\"}]', '12', '0:0:0:0:0:0:0:1', '2019-10-22 14:16:38');
INSERT INTO `sys_log` VALUES ('21', 'admin', '修改用户', 'io.renren.modules.sys.controller.SysUserController.update()', '[{\"userId\":7,\"username\":\"test01\",\"salt\":\"dYMdhl0YUM4ztTEnIpLF\",\"email\":\"test01@qq.com\",\"mobile\":\"15515515506\",\"status\":1,\"roleIdList\":[3,1],\"createUserId\":1}]', '32', '0:0:0:0:0:0:0:1', '2019-10-22 16:27:59');
INSERT INTO `sys_log` VALUES ('22', 'admin', '修改用户', 'io.renren.modules.sys.controller.SysUserController.update()', '[{\"userId\":2,\"username\":\"peng\",\"password\":\"b9ca25de8086c96ee367a24a624e8be180604db1d64f3def33ef74e986e48820\",\"salt\":\"6pSWAG1NJy5Sy0dw5ODE\",\"email\":\"2865900737@qq.com\",\"mobile\":\"15018445543\",\"status\":1,\"roleIdList\":[1],\"createUserId\":1}]', '12', '0:0:0:0:0:0:0:1', '2019-10-22 16:31:47');
INSERT INTO `sys_log` VALUES ('23', 'admin', '修改用户', 'io.renren.modules.sys.controller.SysUserController.update()', '[{\"userId\":2,\"username\":\"peng\",\"salt\":\"6pSWAG1NJy5Sy0dw5ODE\",\"email\":\"2865900737@qq.com\",\"mobile\":\"15018445543\",\"status\":1,\"roleIdList\":[1],\"createUserId\":1}]', '308', '0:0:0:0:0:0:0:1', '2019-10-23 15:05:20');
INSERT INTO `sys_log` VALUES ('24', 'admin', '修改角色', 'io.renren.modules.sys.controller.SysRoleController.update()', '[{\"roleId\":1,\"roleName\":\"test\",\"remark\":\"test\",\"createUserId\":1,\"menuIdList\":[2,15,16,17,18,19,20,21,4,23,24,25,26,5,6,7,8,9,10,11,12,13,14,27,29,30,-666666,1,3]}]', '81', '0:0:0:0:0:0:0:1', '2019-10-23 15:05:48');
INSERT INTO `sys_log` VALUES ('25', 'peng', '保存角色', 'io.renren.modules.sys.controller.SysRoleController.save()', '[{\"roleId\":5,\"roleName\":\"班长\",\"remark\":\"班长\",\"createUserId\":2,\"menuIdList\":[2,15,16,17,18,19,20,21,4,23,24,25,26,5,6,7,8,9,10,11,12,13,14,27,29,30,-666666,1,3],\"createTime\":\"Oct 23, 2019, 3:06:47 PM\"}]', '63', '0:0:0:0:0:0:0:1', '2019-10-23 15:06:47');
INSERT INTO `sys_log` VALUES ('26', 'admin', '删除角色', 'io.renren.modules.sys.controller.SysRoleController.delete()', '[[1]]', '17', '0:0:0:0:0:0:0:1', '2019-10-23 17:19:03');
INSERT INTO `sys_log` VALUES ('27', 'admin', '修改角色', 'io.renren.modules.sys.controller.SysRoleController.update()', '[{\"roleId\":5,\"roleName\":\"测试角色\",\"remark\":\"测试角色\",\"createUserId\":1,\"menuIdList\":[2,15,16,17,18,19,20,21,4,23,24,25,26,5,6,7,8,9,10,11,12,13,14,27,29,30,-666666,1,3]}]', '31', '0:0:0:0:0:0:0:1', '2019-10-23 17:19:26');
INSERT INTO `sys_log` VALUES ('28', 'admin', '修改角色', 'io.renren.modules.sys.controller.SysRoleController.update()', '[{\"roleId\":5,\"roleName\":\"测试角色\",\"remark\":\"测试角色\",\"createUserId\":1,\"menuIdList\":[2,15,16,17,18,19,20,21,4,23,24,25,26,5,6,7,8,9,10,11,12,13,14,27,29,30,-666666,1,3]}]', '34', '0:0:0:0:0:0:0:1', '2019-10-23 17:19:37');
INSERT INTO `sys_log` VALUES ('29', 'admin', '修改用户', 'io.renren.modules.sys.controller.SysUserController.update()', '[{\"userId\":2,\"username\":\"penghcheng\",\"salt\":\"6pSWAG1NJy5Sy0dw5ODE\",\"email\":\"2865900737@qq.com\",\"mobile\":\"15018445543\",\"status\":1,\"roleIdList\":[5],\"createUserId\":1}]', '9', '0:0:0:0:0:0:0:1', '2019-10-23 17:19:51');
INSERT INTO `sys_log` VALUES ('30', 'admin', '修改角色', 'io.renren.modules.sys.controller.SysRoleController.update()', '[{\"roleId\":5,\"roleName\":\"测试角色\",\"remark\":\"测试角色\",\"createUserId\":1,\"menuIdList\":[15,16,17,3,19,20,21,22,4,23,24,25,26,5,6,7,8,9,10,11,12,13,14,27,29,30,-666666,1,2]}]', '26', '0:0:0:0:0:0:0:1', '2019-10-23 17:23:17');
INSERT INTO `sys_log` VALUES ('31', 'admin', '删除用户', 'io.renren.modules.sys.controller.SysUserController.delete()', '[[3]]', '29', '0:0:0:0:0:0:0:1', '2019-10-23 17:34:40');
INSERT INTO `sys_log` VALUES ('32', 'admin', '删除用户', 'io.renren.modules.sys.controller.SysUserController.delete()', '[[5]]', '4', '0:0:0:0:0:0:0:1', '2019-10-23 17:34:44');
INSERT INTO `sys_log` VALUES ('33', 'admin', '删除用户', 'io.renren.modules.sys.controller.SysUserController.delete()', '[[6]]', '3', '0:0:0:0:0:0:0:1', '2019-10-23 17:34:48');
INSERT INTO `sys_log` VALUES ('34', 'admin', '删除用户', 'io.renren.modules.sys.controller.SysUserController.delete()', '[[7]]', '5', '0:0:0:0:0:0:0:1', '2019-10-23 17:34:51');
INSERT INTO `sys_log` VALUES ('35', 'admin', '删除角色', 'io.renren.modules.sys.controller.SysRoleController.delete()', '[[3]]', '10', '0:0:0:0:0:0:0:1', '2019-10-23 17:34:59');
INSERT INTO `sys_log` VALUES ('36', 'admin', '修改角色', 'io.renren.modules.sys.controller.SysRoleController.update()', '[{\"roleId\":5,\"roleName\":\"测试角色\",\"remark\":\"测试角色\",\"createUserId\":1,\"menuIdList\":[15,16,17,3,19,20,21,22,4,23,24,25,26,5,6,7,8,9,10,11,12,13,14,27,29,30,-666666,1,2]}]', '24', '0:0:0:0:0:0:0:1', '2019-10-23 17:35:33');
INSERT INTO `sys_log` VALUES ('37', 'admin', '保存角色', 'io.renren.modules.sys.controller.SysRoleController.save()', '[{\"roleId\":6,\"roleName\":\"经纪人\",\"remark\":\"经纪人\",\"createUserId\":1,\"menuIdList\":[15,16,3,19,20,21,22,4,23,24,25,26,5,6,7,8,9,10,11,12,13,14,27,29,30,-666666,1,2],\"createTime\":\"Oct 23, 2019, 5:40:36 PM\"}]', '18', '0:0:0:0:0:0:0:1', '2019-10-23 17:40:36');
INSERT INTO `sys_log` VALUES ('38', 'admin', '修改用户', 'io.renren.modules.sys.controller.SysUserController.update()', '[{\"userId\":8,\"username\":\"test01\",\"password\":\"8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92\",\"salt\":\"\",\"email\":\"test01@qq.com\",\"mobile\":\"18818818810\",\"status\":1,\"roleIdList\":[],\"createUserId\":1}]', '9', '0:0:0:0:0:0:0:1', '2019-10-23 17:46:38');
INSERT INTO `sys_log` VALUES ('39', 'admin', '修改用户', 'io.renren.modules.sys.controller.SysUserController.update()', '[{\"userId\":9,\"username\":\"test02\",\"password\":\"8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92\",\"salt\":\"\",\"email\":\"test02@qq.com\",\"mobile\":\"18818818811\",\"status\":1,\"roleIdList\":[6],\"createUserId\":1}]', '6', '0:0:0:0:0:0:0:1', '2019-10-23 17:46:57');
INSERT INTO `sys_log` VALUES ('40', 'penghcheng', '保存角色', 'io.renren.modules.sys.controller.SysRoleController.save()', '[{\"roleId\":7,\"roleName\":\"p角色\",\"remark\":\"角色\",\"createUserId\":2,\"menuIdList\":[15,16,3,19,20,21,22,4,23,24,25,26,5,6,7,8,9,10,11,12,13,14,27,29,30,-666666,1,2],\"createTime\":\"Oct 23, 2019, 5:51:11 PM\"}]', '150', '0:0:0:0:0:0:0:1', '2019-10-23 17:51:11');

-- ----------------------------
-- Table structure for sys_menu
-- ----------------------------
DROP TABLE IF EXISTS `sys_menu`;
CREATE TABLE `sys_menu` (
  `menu_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) DEFAULT NULL COMMENT '父菜单ID，一级菜单为0',
  `name` varchar(50) DEFAULT NULL COMMENT '菜单名称',
  `url` varchar(200) DEFAULT NULL COMMENT '菜单URL',
  `perms` varchar(500) DEFAULT NULL COMMENT '授权(多个用逗号分隔，如：user:list,user:create)',
  `type` int(11) DEFAULT NULL COMMENT '类型   0：目录   1：菜单   2：按钮',
  `icon` varchar(50) DEFAULT NULL COMMENT '菜单图标',
  `order_num` int(11) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COMMENT='菜单管理';

-- ----------------------------
-- Records of sys_menu
-- ----------------------------
INSERT INTO `sys_menu` VALUES ('1', '0', '系统管理', null, null, '0', 'system', '0');
INSERT INTO `sys_menu` VALUES ('2', '1', '管理员列表', 'sys/user', null, '1', 'admin', '1');
INSERT INTO `sys_menu` VALUES ('3', '1', '角色管理', 'sys/role', null, '1', 'role', '2');
INSERT INTO `sys_menu` VALUES ('4', '1', '菜单管理', 'sys/menu', null, '1', 'menu', '3');
INSERT INTO `sys_menu` VALUES ('15', '2', '查看', null, 'sys:user:list,sys:user:info', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('16', '2', '新增', null, 'sys:user:save,sys:role:select', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('17', '2', '修改', null, 'sys:user:update,sys:role:select', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('18', '2', '删除', null, 'sys:user:delete', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('19', '3', '查看', null, 'sys:role:list,sys:role:info', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('20', '3', '新增', null, 'sys:role:save,sys:menu:list', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('21', '3', '修改', null, 'sys:role:update,sys:menu:list', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('22', '3', '删除', null, 'sys:role:delete', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('23', '4', '查看', null, 'sys:menu:list,sys:menu:info', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('24', '4', '新增', null, 'sys:menu:save,sys:menu:select', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('25', '4', '修改', null, 'sys:menu:update,sys:menu:select', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('26', '4', '删除', null, 'sys:menu:delete', '2', null, '0');
INSERT INTO `sys_menu` VALUES ('27', '1', '参数管理', 'sys/config', 'sys:config:list,sys:config:info,sys:config:save,sys:config:update,sys:config:delete', '1', 'config', '6');
INSERT INTO `sys_menu` VALUES ('29', '1', '系统日志', 'sys/log', 'sys:log:list', '1', 'log', '7');
INSERT INTO `sys_menu` VALUES ('30', '1', '文件上传', 'oss/oss', 'sys:oss:all', '1', 'oss', '6');

-- ----------------------------
-- Table structure for sys_oss
-- ----------------------------
DROP TABLE IF EXISTS `sys_oss`;
CREATE TABLE `sys_oss` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `url` varchar(200) DEFAULT NULL COMMENT 'URL地址',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件上传';

-- ----------------------------
-- Records of sys_oss
-- ----------------------------

-- ----------------------------
-- Table structure for sys_role
-- ----------------------------
DROP TABLE IF EXISTS `sys_role`;
CREATE TABLE `sys_role` (
  `role_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) DEFAULT NULL COMMENT '角色名称',
  `remark` varchar(100) DEFAULT NULL COMMENT '备注',
  `create_user_id` bigint(20) DEFAULT NULL COMMENT '创建者ID',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='角色';

-- ----------------------------
-- Records of sys_role
-- ----------------------------
INSERT INTO `sys_role` VALUES ('5', '测试角色', '测试角色', '1', '2019-10-23 15:06:47');

-- ----------------------------
-- Table structure for sys_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `sys_role_menu`;
CREATE TABLE `sys_role_menu` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) DEFAULT NULL COMMENT '角色ID',
  `menu_id` bigint(20) DEFAULT NULL COMMENT '菜单ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=903 DEFAULT CHARSET=utf8mb4 COMMENT='角色与菜单对应关系';

-- ----------------------------
-- Records of sys_role_menu
-- ----------------------------
INSERT INTO `sys_role_menu` VALUES ('292', '5', '15');
INSERT INTO `sys_role_menu` VALUES ('293', '5', '16');
INSERT INTO `sys_role_menu` VALUES ('294', '5', '17');
INSERT INTO `sys_role_menu` VALUES ('295', '5', '3');
INSERT INTO `sys_role_menu` VALUES ('296', '5', '19');
INSERT INTO `sys_role_menu` VALUES ('297', '5', '20');
INSERT INTO `sys_role_menu` VALUES ('298', '5', '21');
INSERT INTO `sys_role_menu` VALUES ('299', '5', '22');
INSERT INTO `sys_role_menu` VALUES ('300', '5', '4');
INSERT INTO `sys_role_menu` VALUES ('301', '5', '23');
INSERT INTO `sys_role_menu` VALUES ('302', '5', '24');
INSERT INTO `sys_role_menu` VALUES ('303', '5', '25');
INSERT INTO `sys_role_menu` VALUES ('304', '5', '26');
INSERT INTO `sys_role_menu` VALUES ('315', '5', '27');
INSERT INTO `sys_role_menu` VALUES ('316', '5', '29');
INSERT INTO `sys_role_menu` VALUES ('317', '5', '30');
INSERT INTO `sys_role_menu` VALUES ('318', '5', '-666666');
INSERT INTO `sys_role_menu` VALUES ('319', '5', '1');
INSERT INTO `sys_role_menu` VALUES ('320', '5', '2');

-- ----------------------------
-- Table structure for sys_user
-- ----------------------------
DROP TABLE IF EXISTS `sys_user`;
CREATE TABLE `sys_user` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `password` varchar(100) DEFAULT NULL COMMENT '密码',
  `salt` varchar(20) DEFAULT NULL COMMENT '盐',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(100) DEFAULT NULL COMMENT '手机号',
  `status` tinyint(4) DEFAULT NULL COMMENT '状态  0：禁用   1：正常',
  `create_user_id` bigint(20) DEFAULT NULL COMMENT '创建者ID',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='系统用户';

-- ----------------------------
-- Records of sys_user
-- ----------------------------
INSERT INTO `sys_user` VALUES ('1', 'admin', '$2y$12$iQngOKbEZTJR3oMmXdp0.eN83D20bLPs/d.03A4/5DTpaQcaERNcq', 'YzcmCZNvbXocrsz9dm8e', 'root@qq.com', '13612345678', '1', '1', '2016-11-11 11:11:11');
INSERT INTO `sys_user` VALUES ('2', 'test', '$2y$12$j0vNJRyxdRlfTuenypXf9OKzW2YMahNrFI.aUXRiQpEexus.mAfIW', '6pSWAG1NJy5Sy0dw5ODE', 'test@qq.com', '18012345678', '1', '1', '2019-10-18 14:16:01');

-- ----------------------------
-- Table structure for sys_user_role
-- ----------------------------
DROP TABLE IF EXISTS `sys_user_role`;
CREATE TABLE `sys_user_role` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL COMMENT '用户ID',
  `role_id` bigint(20) DEFAULT NULL COMMENT '角色ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COMMENT='用户与角色对应关系';

-- ----------------------------
-- Records of sys_user_role
-- ----------------------------
INSERT INTO `sys_user_role` VALUES ('17', '2', '5');

-- ----------------------------
-- Table structure for sys_user_token
-- ----------------------------
DROP TABLE IF EXISTS `sys_user_token`;
CREATE TABLE `sys_user_token` (
  `user_id` bigint(20) NOT NULL,
  `token` varchar(100) NOT NULL COMMENT 'token',
  `expire_time` datetime DEFAULT NULL COMMENT '过期时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统用户Token';

-- ----------------------------
-- Records of sys_user_token
-- ----------------------------
INSERT INTO `sys_user_token` VALUES ('1', '8b5404d67b8852383edf3dcac0e3f730', '2019-10-24 05:34:32', '2019-10-23 17:34:32');
INSERT INTO `sys_user_token` VALUES ('2', '8a7eddc6c40cb7cf69c91d0d3b530d66', '2019-10-24 05:50:38', '2019-10-23 17:50:38');
INSERT INTO `sys_user_token` VALUES ('3', 'ac84462f90f4d1e0f25c53bcf8b54d31', '2019-10-19 02:32:33', '2019-10-18 14:32:33');
INSERT INTO `sys_user_token` VALUES ('5', 'af2fdb238b7caf9343c88e4b39e1c8f0', '2019-10-19 05:47:35', '2019-10-18 17:47:35');

-- ----------------------------
-- Table structure for tb_user
-- ----------------------------
DROP TABLE IF EXISTS `tb_user`;
CREATE TABLE `tb_user` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `mobile` varchar(20) NOT NULL COMMENT '手机号',
  `password` varchar(64) DEFAULT NULL COMMENT '密码',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户';

-- ----------------------------
-- Records of tb_user
-- ----------------------------
INSERT INTO `tb_user` VALUES ('1', 'mark', '13612345678', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '2017-03-23 22:37:41');
