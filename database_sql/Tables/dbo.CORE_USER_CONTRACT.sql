CREATE TABLE [dbo].[CORE_USER_CONTRACT]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[TS_CREATED] [datetime] NOT NULL CONSTRAINT [DF_CORE_USER_CONTRACT_TS_CREATED] DEFAULT (getdate()),
[TS_LASTMODIFIED] [datetime] NULL CONSTRAINT [DF_CORE_USER_CONTRACT_TS_LASTMODIFIED] DEFAULT (getdate()),
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_CORE_USER_CONTRACT_ACTIVE] DEFAULT ((1)),
[FK_CORE_USER] [int] NOT NULL,
[FK_CORE_DROPDOWNVALUE_USERCONTRACTTYPE] [int] NULL,
[HOURS] [float] NULL,
[HOURS_WEEKLY] [float] NULL,
[HOURLY_WAGE] [decimal] (30, 15) NULL,
[DATE_START] [datetime] NULL,
[DATE_END] [datetime] NULL,
[DATE_PROBATION] [datetime] NULL
) ON [PRIMARY]
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_NULLS ON
GO
CREATE TRIGGER [dbo].[TR_CORE_USER_CONTRACT_TS_LASTMODIFIED]
						   ON  [dbo].[CORE_USER_CONTRACT]
						   AFTER UPDATE
						AS 
						BEGIN
							SET NOCOUNT ON;

							UPDATE T SET 
								T.TS_LASTMODIFIED = GETDATE()
							FROM 
								CORE_USER_CONTRACT T
							INNER JOIN INSERTED I ON T.ID = I.ID
						END
GO
ALTER TABLE [dbo].[CORE_USER_CONTRACT] ADD CONSTRAINT [PK_CORE_USER_CONTRACT] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
ALTER TABLE [dbo].[CORE_USER_CONTRACT] ADD CONSTRAINT [FK_CORE_USER_CONTRACT_CORE_DROPDOWNVALUE] FOREIGN KEY ([FK_CORE_DROPDOWNVALUE_USERCONTRACTTYPE]) REFERENCES [dbo].[CORE_DROPDOWNVALUE] ([ID])
GO
ALTER TABLE [dbo].[CORE_USER_CONTRACT] ADD CONSTRAINT [FK_CORE_USER_CONTRACT_CORE_USER] FOREIGN KEY ([FK_CORE_USER]) REFERENCES [dbo].[CORE_USER] ([ID])
GO
