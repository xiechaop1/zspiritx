<?php

$tmp = '[{"role":"assistant","content":"适合四年级同学的题目，科目是：数学，请出10道，题目随机一些，尽可能规避历史已经出过的"},{"role":"assistant","content":"#输出格式#输出题目、题型、标准答案和近似的三个选项答案，用ABCD表示。输出格式为JSON"},{"role":"assistant","content":"#输出样例#[{\"SUBJECT\":\"SUBJECT1\",\"OPTIONS\":{\"A\":\"AAA\",\"B\":\"BBB\",\"C\":\"CCC\",\"D\":\"DDD\"},\"ANSWER\":\"A\",\"TYPE\":\"题型\"},{\"SUBJECT\":\"SUBJECT2\",\"OPTIONS\":{\"A\":\"AAA\",\"B\":\"BBB\",\"C\":\"CCC\",\"D\":\"DDD\"},\"ANSWER\":\"A\",\"TYPE\":\"题型\"}]"},{"role":"system","content":"你是一个小灵镜，负责出题和解答"},{"role":"user","content":"生成10道数学计算或者方程类题目"}]';

var_dump(json_decode($tmp, true));
