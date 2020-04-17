CREATE TABLE [dbo].[STANDARD_TASK_LIST]
(
[ID] [int] NOT NULL IDENTITY(1, 1),
[NAME] [nvarchar] (50) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
[ACTIVE] [bit] NOT NULL CONSTRAINT [DF_STANDARD_TASK_LIST_ACTIVE] DEFAULT ((1))
) ON [PRIMARY]
GO
ALTER TABLE [dbo].[STANDARD_TASK_LIST] ADD CONSTRAINT [PK_STANDARD_TASK_LIST] PRIMARY KEY CLUSTERED  ([ID]) ON [PRIMARY]
GO
