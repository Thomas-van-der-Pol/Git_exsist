SET IDENTITY_INSERT [dbo].[CORE_SETTING] ON
INSERT INTO [dbo].[CORE_SETTING] ([ID], [ACTIVE], [SEQUENCE], [TS_CREATED], [TS_LASTMODIFIED], [FK_CORE_SETTING_GROUP], [FK_CORE_SETTING_TYPE], [DESCRIPTION], [REQUIRED]) VALUES (4, 1, 1, '2020-04-23 09:27:19.110', '2020-04-23 09:27:19.110', 2, 1, N'AUTHORIZATION_CODE', 0)
INSERT INTO [dbo].[CORE_SETTING] ([ID], [ACTIVE], [SEQUENCE], [TS_CREATED], [TS_LASTMODIFIED], [FK_CORE_SETTING_GROUP], [FK_CORE_SETTING_TYPE], [DESCRIPTION], [REQUIRED]) VALUES (5, 1, 2, '2020-04-23 09:27:19.110', '2020-04-23 09:30:37.647', 2, 1, N'ACCESS_TOKEN', 0)
INSERT INTO [dbo].[CORE_SETTING] ([ID], [ACTIVE], [SEQUENCE], [TS_CREATED], [TS_LASTMODIFIED], [FK_CORE_SETTING_GROUP], [FK_CORE_SETTING_TYPE], [DESCRIPTION], [REQUIRED]) VALUES (6, 1, 3, '2020-04-23 09:27:19.110', '2020-04-23 09:30:38.453', 2, 1, N'REFRESH_TOKEN', 0)
INSERT INTO [dbo].[CORE_SETTING] ([ID], [ACTIVE], [SEQUENCE], [TS_CREATED], [TS_LASTMODIFIED], [FK_CORE_SETTING_GROUP], [FK_CORE_SETTING_TYPE], [DESCRIPTION], [REQUIRED]) VALUES (7, 1, 4, '2020-04-23 09:27:19.110', '2020-04-23 09:30:39.823', 2, 1, N'EXPIRES_IN', 0)
SET IDENTITY_INSERT [dbo].[CORE_SETTING] OFF