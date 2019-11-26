<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActivationToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //我们将使用随机字符来生成用户的激活令牌，因此这里的激活令牌字段需要为 string 类型，在用户 成功激活以后，我们还会对激活令牌进行清空，避免用户进行多次使用
            $table->string('activation_token')->nullable()->comment('激活令牌');
            $table->boolean('activated')->default(false)->comment('激活状态');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activation_token');
            $table->dropColumn('activated');
        });
    }
}
