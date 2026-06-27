-- MariaDB dump converted from SQL Server
-- Database: account
-- Generated: 2026-06-27

CREATE DATABASE IF NOT EXISTS `account` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `account`;

SET FOREIGN_KEY_CHECKS=0;

-- Table: `AccCard_History`
DROP TABLE IF EXISTS `AccCard_History`;
CREATE TABLE `AccCard_History` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cAccName` VARCHAR(60) NOT NULL,
  `cCardCode` VARCHAR(30) NOT NULL,
  `dDateTime` DATETIME NULL,
  `cIP` CHAR(15) NULL,
  `iValue` INT NULL,
  `iFlag` SMALLINT NULL,
  `cAccGive` VARCHAR(60) NULL,
  `cCardSeri` VARCHAR(30) NULL,
  `status` INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `AccIP`
DROP TABLE IF EXISTS `AccIP`;
CREATE TABLE `AccIP` (
  `Ip` VARCHAR(16) NULL,
  `Flag` SMALLINT NULL,
  `GFlag` SMALLINT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `Account_Habitus`
DROP TABLE IF EXISTS `Account_Habitus`;
CREATE TABLE `Account_Habitus` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cAccName` VARCHAR(60) NOT NULL,
  `iFlag` TINYINT NULL DEFAULT 0,
  `iLeftSecond` BIGINT NULL DEFAULT 0,
  `nExtPoint` BIGINT NULL DEFAULT 0,
  `dBeginDate` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `iLeftMonth` BIGINT NULL DEFAULT 0,
  `dEndDate` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `iClientID` BIGINT NULL DEFAULT 0,
  `isUse` TINYINT NULL DEFAULT 0,
  `iAddDay` BIGINT NULL DEFAULT 0,
  `iAddHour` BIGINT NULL DEFAULT 0,
  `iMoney` BIGINT NULL DEFAULT 0,
  `nExtPoint1` INT NULL DEFAULT 0,
  `nExtPoint2` BIGINT NOT NULL DEFAULT 0,
  `nExtPoint3` BIGINT NOT NULL DEFAULT 0,
  `nExtPoint4` BIGINT NOT NULL DEFAULT 0,
  `nExtPoint5` BIGINT NOT NULL DEFAULT 0,
  `nExtPoint6` BIGINT NOT NULL DEFAULT 0,
  `nExtPoint7` BIGINT NOT NULL DEFAULT 0,
  `nExtPoint8` BIGINT NOT NULL DEFAULT 0,
  `nExtPoint9` BIGINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `Account_Info`
DROP TABLE IF EXISTS `Account_Info`;
CREATE TABLE `Account_Info` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cAccName` VARCHAR(32) NOT NULL,
  `cPassWord` VARCHAR(99) NOT NULL,
  `cSecPassword` VARCHAR(99) NOT NULL,
  `cRealName` VARCHAR(32) NULL DEFAULT 'name',
  `dBirthDay` DATETIME NULL DEFAULT NULL,
  `cArea` VARCHAR(60) NULL DEFAULT NULL,
  `cIDNum` VARCHAR(30) NULL DEFAULT NULL,
  `dRegDate` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `cPhone` VARCHAR(50) NULL,
  `iClientID` BIGINT NULL DEFAULT 0,
  `dLoginDate` VARCHAR(30) NULL DEFAULT NULL,
  `dLogoutDate` VARCHAR(30) NULL DEFAULT NULL,
  `iTimeCount` TINYINT NULL,
  `cQuestion` VARCHAR(250) NULL,
  `cAnswer` VARCHAR(250) NULL,
  `cSex` VARCHAR(4) NULL,
  `cDegree` VARCHAR(16) NULL,
  `cEMail` VARCHAR(128) NULL,
  `iMoney` INT NULL DEFAULT 0,
  `iYuanbao` INT NULL,
  `StatusVerify` CHAR(10) NULL,
  `cPassWord2` VARCHAR(99) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `AccountLog`
DROP TABLE IF EXISTS `AccountLog`;
CREATE TABLE `AccountLog` (
  `iID` BIGINT NOT NULL AUTO_INCREMENT,
  `szAccName` VARCHAR(32) NOT NULL,
  `iOperation` BIGINT NOT NULL,
  `szOpAddr` VARCHAR(16) NULL,
  `opData1` BIGINT NULL,
  `opData2` VARCHAR(32) NULL,
  `optime` DATETIME NOT NULL,
  PRIMARY KEY (`szAccName`),
  KEY `idx_iID` (`iID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `Admins`
DROP TABLE IF EXISTS `Admins`;
CREATE TABLE `Admins` (
  `szHostAddr` VARCHAR(16) NULL,
  `szUserName` VARCHAR(16) NOT NULL,
  `szPassword` VARCHAR(16) NOT NULL,
  `ePriority` TINYINT NOT NULL DEFAULT 0,
  `eLoggedin` TINYINT NOT NULL DEFAULT 0,
  `dLastLoginTime` DATETIME NULL,
  `dLastLogoutTime` DATETIME NULL,
  PRIMARY KEY (`szUserName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `BatchNumber`
DROP TABLE IF EXISTS `BatchNumber`;
CREATE TABLE `BatchNumber` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cBatchNumberCode` VARCHAR(6) NOT NULL,
  `cBatchNumberCodeDescrip` VARCHAR(256) NULL,
  `iFlag` INT NULL,
  `dDate` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `Card_History`
DROP TABLE IF EXISTS `Card_History`;
CREATE TABLE `Card_History` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cCardCode` VARCHAR(30) NOT NULL,
  `dDate` DATETIME NULL,
  `cUserName` VARCHAR(32) NULL,
  `iFlag` INT NULL,
  `Money` INT NULL,
  `KNBTruoc` INT NULL,
  `KNBSau` INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `CardCreateRecord`
DROP TABLE IF EXISTS `CardCreateRecord`;
CREATE TABLE `CardCreateRecord` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cUserID` VARCHAR(16) NOT NULL,
  `binFile` LONGBLOB NULL,
  `dDate` DATETIME NULL,
  `cProductCode` VARCHAR(2) NULL,
  `cCardTypeCode` VARCHAR(4) NULL,
  `cBatchNumberCode` VARCHAR(6) NULL,
  `cBeginCardCode` VARCHAR(20) NOT NULL,
  `cEndCardCode` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `CardInfo`
DROP TABLE IF EXISTS `CardInfo`;
CREATE TABLE `CardInfo` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cCardCode` VARCHAR(30) NOT NULL,
  `cCardPassWord` VARCHAR(16) NOT NULL,
  `iFlag` TINYINT(1) NULL DEFAULT 0,
  `iHoldSecond` INT NULL,
  `iHoldMonth` TINYINT NULL,
  `cOverdueDate` DATETIME NULL,
  `cDate` DATETIME NULL,
  `AccName` VARCHAR(50) NULL,
  `Money` BIGINT NULL,
  `cType` TINYINT(1) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `CardType`
DROP TABLE IF EXISTS `CardType`;
CREATE TABLE `CardType` (
  `iid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cTypeCode` VARCHAR(4) NOT NULL,
  `cTypeDescrip` VARCHAR(256) NULL,
  `iFlag` SMALLINT NULL,
  `iValue` INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `ChangePName`
DROP TABLE IF EXISTS `ChangePName`;
CREATE TABLE `ChangePName` (
  `Id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Account` VARCHAR(64) NULL,
  `OldName` VARCHAR(64) NULL,
  `NewName` VARCHAR(64) NULL,
  `DateTime` VARCHAR(150) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `CTC_Data`
DROP TABLE IF EXISTS `CTC_Data`;
CREATE TABLE `CTC_Data` (
  `Id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `KeyTuan` VARCHAR(50) NULL,
  `BangHoi` VARCHAR(150) NULL,
  `GhiChu` VARCHAR(50) NULL,
  `DateTime` VARCHAR(150) NULL,
  `nThuong` INT NULL,
  `TongNameID` VARCHAR(64) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `CTC_Log`
DROP TABLE IF EXISTS `CTC_Log`;
CREATE TABLE `CTC_Log` (
  `Id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `KeyTuan` VARCHAR(50) NULL,
  `BangHoi` VARCHAR(150) NULL,
  `GhiChu` VARCHAR(50) NULL,
  `DateTime` VARCHAR(150) NULL,
  `TongNameID` VARCHAR(64) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `CTC_NopKCL`
DROP TABLE IF EXISTS `CTC_NopKCL`;
CREATE TABLE `CTC_NopKCL` (
  `Id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Account` VARCHAR(150) NULL,
  `RoleName` VARCHAR(150) NULL,
  `SoKCL` INT NULL,
  `DateTime` VARCHAR(150) NULL,
  `TongName` VARCHAR(150) NULL,
  `TongNameID` VARCHAR(64) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `CTC_TongInfo`
DROP TABLE IF EXISTS `CTC_TongInfo`;
CREATE TABLE `CTC_TongInfo` (
  `Id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `TongName` VARCHAR(150) NULL,
  `SoKCL` INT NULL,
  `TongNameID` VARCHAR(64) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `DaiLyKNB`
DROP TABLE IF EXISTS `DaiLyKNB`;
CREATE TABLE `DaiLyKNB` (
  `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `TenDangNhap` VARCHAR(50) NULL,
  `MatKhau` VARCHAR(50) NULL,
  `HoVaTen` VARCHAR(250) NULL,
  `NganHang` VARCHAR(50) NULL,
  `SoTaiKhoan` VARCHAR(50) NULL,
  `ChiNhanh` VARCHAR(250) NULL,
  `Zalo` VARCHAR(50) NULL,
  `Phone` VARCHAR(50) NULL,
  `Facebook` VARCHAR(250) NULL,
  `iYuanBao` INT NULL,
  `IsAdmin` INT NULL,
  `ChietKhau` INT NULL,
  `KichHoat` TINYINT(1) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `DaiLyNapThe`
DROP TABLE IF EXISTS `DaiLyNapThe`;
CREATE TABLE `DaiLyNapThe` (
  `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `AccountDL` VARCHAR(50) NULL,
  `AccountGammer` VARCHAR(50) NULL,
  `DateNap` DATETIME NULL,
  `TrangThai` INT NULL,
  `SoKNB` INT NULL,
  `SoKNBKM` INT NULL,
  `SoTien` INT NULL,
  `KNBTruoc` INT NULL,
  `KNBSau` INT NULL,
  `NoiDung` VARCHAR(250) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `DaiLyNgayVang`
DROP TABLE IF EXISTS `DaiLyNgayVang`;
CREATE TABLE `DaiLyNgayVang` (
  `CfName` VARCHAR(50) NULL,
  `DateTime` DATETIME NULL,
  `EndDate` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `EquipBaseInfo`
DROP TABLE IF EXISTS `EquipBaseInfo`;
CREATE TABLE `EquipBaseInfo` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `iMainID` BIGINT NOT NULL,
  `iClass` INT NOT NULL,
  `cInfoText` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `EquipEfficInfo`
DROP TABLE IF EXISTS `EquipEfficInfo`;
CREATE TABLE `EquipEfficInfo` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `iMainID` BIGINT NOT NULL,
  `iClass` INT NOT NULL,
  `cInfoText` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `Equipments`
DROP TABLE IF EXISTS `Equipments`;
CREATE TABLE `Equipments` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cUserCode` VARCHAR(100) NOT NULL,
  `iEquipClassCode` INT NOT NULL,
  `iLocal` INT NOT NULL,
  `iX` INT NOT NULL,
  `iY` INT NOT NULL,
  `iEquipCode` BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `EquipRequireInfo`
DROP TABLE IF EXISTS `EquipRequireInfo`;
CREATE TABLE `EquipRequireInfo` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `iMainID` BIGINT NOT NULL,
  `iClass` INT NOT NULL,
  `cInfoText` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `FightSkill`
DROP TABLE IF EXISTS `FightSkill`;
CREATE TABLE `FightSkill` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cUserCode` VARCHAR(100) NOT NULL,
  `iFightSkill` INT NOT NULL,
  `iFightSkillLevel` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `Friend_List`
DROP TABLE IF EXISTS `Friend_List`;
CREATE TABLE `Friend_List` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cUserCode` VARCHAR(100) NOT NULL,
  `cFriendCode` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `gamerid`
DROP TABLE IF EXISTS `gamerid`;
CREATE TABLE `gamerid` (
  `iID` BIGINT NOT NULL AUTO_INCREMENT,
  `szName` VARCHAR(32) NOT NULL,
  PRIMARY KEY (`szName`),
  KEY `idx_iID` (`iID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `LichSuDoiSDT`
DROP TABLE IF EXISTS `LichSuDoiSDT`;
CREATE TABLE `LichSuDoiSDT` (
  `Id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Date` DATETIME NULL,
  `AccName` VARCHAR(50) NULL,
  `DaiLyName` VARCHAR(50) NULL,
  `SDTCu` VARCHAR(50) NULL,
  `SDTMoi` VARCHAR(50) NULL,
  `PhiKNB` INT NULL,
  `KNBTruoc` INT NULL,
  `KNBSau` INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `LifeSkill`
DROP TABLE IF EXISTS `LifeSkill`;
CREATE TABLE `LifeSkill` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cUserCode` VARCHAR(100) NOT NULL,
  `iLifeSkill` INT NOT NULL,
  `iLifeSkillLevel` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `MaxNum`
DROP TABLE IF EXISTS `MaxNum`;
CREATE TABLE `MaxNum` (
  `cProductCode` VARCHAR(4) NOT NULL,
  `cTypeCode` VARCHAR(4) NOT NULL,
  `cOtherTypeCode` VARCHAR(4) NOT NULL,
  `cSaleTypeCode` VARCHAR(4) NOT NULL,
  `iNum` INT NOT NULL,
  PRIMARY KEY (`cProductCode`, `cTypeCode`, `cOtherTypeCode`, `cSaleTypeCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `OtherType`
DROP TABLE IF EXISTS `OtherType`;
CREATE TABLE `OtherType` (
  `iid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cOtherTypeCode` VARCHAR(4) NOT NULL,
  `cOtherTypeDescrip` VARCHAR(256) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `PerformIP`
DROP TABLE IF EXISTS `PerformIP`;
CREATE TABLE `PerformIP` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cPerformIP` VARCHAR(16) NOT NULL,
  `cMemo` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `Product`
DROP TABLE IF EXISTS `Product`;
CREATE TABLE `Product` (
  `iid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cProductCode` VARCHAR(2) NOT NULL,
  `cProductDescrip` VARCHAR(256) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `Role_Info`
DROP TABLE IF EXISTS `Role_Info`;
CREATE TABLE `Role_Info` (
  `iid` BIGINT NOT NULL,
  `cUserCode` VARCHAR(100) NOT NULL,
  `bSex` VARCHAR(10) NOT NULL,
  `cAlias` VARCHAR(100) NULL,
  `iSect` INT NULL,
  `iSectRole` INT NULL,
  `iGroupRole` INT NULL,
  `iLoginLocal` INT NULL,
  `cPartnerCode` VARCHAR(100) NULL,
  `iMoney` BIGINT NOT NULL,
  `iFiveProp` INT NULL,
  `iTeam` INT NULL,
  `iFightLevel` INT NULL,
  `iFightExp` INT NULL,
  `iLeadLeveal` INT NULL,
  `iLeadExp` INT NULL,
  `iLiveExp` INT NULL,
  `iPower` INT NULL,
  `iAgility` INT NULL,
  `iOuter` INT NULL,
  `iInside` INT NULL,
  `iLuck` INT NULL,
  `iMaxLife` INT NULL,
  `iMaxStamina` INT NULL,
  `iMaxInner` INT NULL,
  `iLeftProp` INT NULL,
  `iLeftFight` INT NULL,
  `iLeftLife` INT NULL,
  PRIMARY KEY (`iid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `SaleType`
DROP TABLE IF EXISTS `SaleType`;
CREATE TABLE `SaleType` (
  `iid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cSaleTypeCode` VARCHAR(4) NOT NULL,
  `cSaleTypeDescrip` VARCHAR(256) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `ServerList`
DROP TABLE IF EXISTS `ServerList`;
CREATE TABLE `ServerList` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cServerName` VARCHAR(30) NOT NULL,
  `cPassword` VARCHAR(99) NULL,
  `cIP` VARCHAR(16) NULL,
  `iPort` SMALLINT NULL,
  `cMemo` VARCHAR(200) NULL,
  `dwGamers` BIGINT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `StatusCode`
DROP TABLE IF EXISTS `StatusCode`;
CREATE TABLE `StatusCode` (
  `Id` INT NOT NULL,
  `Name` VARCHAR(255) NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `Task_List`
DROP TABLE IF EXISTS `Task_List`;
CREATE TABLE `Task_List` (
  `iid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cUserCode` VARCHAR(100) NOT NULL,
  `iTaskCode` INT NOT NULL,
  `iDegree` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: `UserManager`
DROP TABLE IF EXISTS `UserManager`;
CREATE TABLE `UserManager` (
  `iid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cUserCode` VARCHAR(20) NOT NULL,
  `cUserName` VARCHAR(30) NULL,
  `iRole` SMALLINT NULL,
  `iFlag` TINYINT(1) NULL,
  `cPassWord` VARCHAR(20) NULL,
  `cEmail` VARCHAR(128) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foreign Key Constraints
ALTER TABLE `DaiLyNapThe` ADD CONSTRAINT `FK_DaiLyNapThe_StatusCode` FOREIGN KEY (`TrangThai`) REFERENCES `StatusCode` (`Id`);

SET FOREIGN_KEY_CHECKS=1;