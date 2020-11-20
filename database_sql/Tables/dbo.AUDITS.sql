CREATE TABLE [dbo].[AUDITS]
(
[id] [bigint] NOT NULL IDENTITY(1, 1),
[user_type] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[user_id] [bigint] NULL,
[event] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
[auditable_type] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NOT NULL,
[auditable_id] [bigint] NOT NULL,
[old_values] [nvarchar] (max) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[new_values] [nvarchar] (max) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[url] [nvarchar] (max) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[ip_address] [nvarchar] (45) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[user_agent] [nvarchar] (1023) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[tags] [nvarchar] (255) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
[created_at] [datetime] NULL,
[updated_at] [datetime] NULL
) ON [PRIMARY]
GO
ALTER TABLE [dbo].[AUDITS] ADD CONSTRAINT [PK__audits__3213E83FA3200E31] PRIMARY KEY CLUSTERED  ([id]) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [audits_auditable_type_auditable_id_index] ON [dbo].[AUDITS] ([auditable_type], [auditable_id]) ON [PRIMARY]
GO
CREATE NONCLUSTERED INDEX [audits_user_id_user_type_index] ON [dbo].[AUDITS] ([user_id], [user_type]) ON [PRIMARY]
GO
