CREATE TABLE [dbo].[PASSWORD_RESET]
(
[email] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
[token] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
[created_at] [datetime] NULL
) ON [PRIMARY]
GO
