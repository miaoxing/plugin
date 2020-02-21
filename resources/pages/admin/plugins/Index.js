import React from "react";
import app from "plugins/app/resources/modules/app";
import Table from "antdx-table";
import {Page} from "@miaoxing/page";
import {TableStatusCheckbox} from "@miaoxing/table";

export default class extends React.Component {
  render() {
    return <Page>
      <Table
        url={app.curApiIndexUrl()}
        columns={[
          {
            title: '名称',
            dataIndex: 'name'
          },
          {
            title: '标识',
            dataIndex: 'id',
          },
          {
            title: '版本',
            dataIndex: 'version',
          },
          {
            title: '描述',
            dataIndex: 'description',
          },
          {
            title: '安装',
            dataIndex: 'action',
            render: (id, row) => (
              row.builtIn ? <span title="内置插件,无需安装">-</span> :
                <TableStatusCheckbox url={app.url('admin-api/plugins/update')} row={row} name="installed"/>
            )
          },
        ]}
      />
    </Page>;
  }
}
